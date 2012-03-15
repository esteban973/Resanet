<?php

namespace Resanet\PaiementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\PaiementBundle\Classes\ConnexionPayPal;
use Symfony\Component\HttpFoundation\Response;
use Resanet\MainBundle\Entity\Reservation;
use Resanet\MainBundle\Entity\Paiement;
use Resanet\PaiementBundle\Classes\Token;

class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ResanetPaiementBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function createPaiement($reservation){
        $url=$this->container->getParameter('url');
        $username=$this->container->getParameter('username');
        $pwd=$this->container->getParameter('password');
        $signature=$this->container->getParameter('signature');
        $version=$this->container->getParameter('version');
        $connPayPal=new ConnexionPayPal();
        $connPayPal->setCommun($url, $version, $pwd, $username, $signature, $reservation);
        return $connPayPal;
        
    }
    
    /*
     * faire une requete de paiement, va faire une demande de paiement 
     * @todo si la requete a fonctionné ou pas
     * @param $reservation
     * @return redirect vers URL
     */
    public function getTokenAction($reservation){
       $tekken=Token::generateToken($reservation->getId().$this->getRequest()->getSession()->getId());
       $connPayPal=$this->createPaiement($reservation);
       $urlReturn =$this->get('router')->generate('ResanetPaiementBundle_doPayment',array('id'=>$reservation->getId(),'token'=>$tekken), true);
       $urlCancel =$this->get('router')->generate('ResanetSiteBundle_reservationErreur',array(), true);
       $requete=$connPayPal->getRequeteToken( $urlReturn, $urlCancel);
       $resultat=$connPayPal->connectionApiPaypal($requete);
       $token=$resultat['TOKEN'];
       return $this->redirect('https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token='.$token);
    }
    
    
    /*
     * faire une requete de paiement, va faire une demande de paiement 
     * @todo si la requete a fonctionné ou pas
     * @param $idreservation
     * @return redirect vers URL
     */
    
    public function doPaymentAction($id, $token){
      if (!(Token::isCsrfTokenValid($id.$this->getRequest()->getSession()->getId(), $token))){
          $erreur='C\'est mal ce que vous faites.';
          return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
      }
        
        
      $request=$this->getRequest();
      $em=$this->getDoctrine()->getEntityManager();
      $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
      
      //vérifier si l'Id existe ou pas
      if (!$resa){
          $erreur='Aucune réservation ne correspond à vôtre requête.';
          return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
      }
      
      if ($resa->getEtat()!='Annulée'){
          $erreur='Votre réservation ne peut être modifiée.';
          return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
      }
      
      //si oui modifier, vérifier si les chambres de la résa sont toujours disponibles ou pas
      $pb=array();
      $date1=$resa->getDateArrivee()->format('Y-m-d');
      $date2=$resa->getDateDepart()->format('Y-m-d');
      foreach ($resa->getChambres() as $chambre){
          if (!($chambre->estDispoPeriode($date1, $date2))){
              $pb[]=$chambre->getCategorie();
              $resa->getChambres()->removeElement($chambre);
              
          }
      }
     $resa->setEtat('Option');
     $em->flush();
      
      //recherche d'autres chambres, si aucune disponible, cancel la réservation
      if (count($pb)>0){
          $bool=true;
          foreach($pb as $key=>$cat){
              foreach ($cat->getChambres() as $chambre){
                  if ($chambre->estDispoPeriode($date1, $date2)){
                      $resa->addChambre($chambre);
                      unset($pb[$key]);
                      break;
                  }
              }
          }
         
          if (count($pb)>0){
             
             $em->remove($resa);
             $em->flush();
              $erreur='Les chambres ne sont plus disponibles';
              return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
          } else {
             $em->flush();
          }
        } 
      
       $connPayPal=$this->createPaiement($resa);
       $tekken=$request->get('token');
       $payerId=$request->get('PayerID');
       $requete=$connPayPal->getPaiement($tekken, $payerId);
       $resultat=$connPayPal->connectionApiPaypal($requete);
       if("SUCCESS" == strtoupper($resultat["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($resultat["ACK"])) {
           $paiement=new Paiement();
           $paiement->setDatePaiement(new \DateTime(date('Y-m-d')));
           $paiement->setMontant($resa->getTotal());
           $paiement->setMoyen('PayPal');
           $paiement->setReservation($resa);
           $resa->addPaiement($paiement);
           $resa->setEtat('Confirmée');
           $em->persist($paiement);
           $em->flush();
           return $this->forward('ResanetSiteBundle:ReservationFront:reservationConfirmation', array('resa'=>$resa));
            } else  {
                $erreur='Le paiement n\'a pu être effectué.';
                return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
        }              
       
    }
    
 
    
    
}
