<?php
 /**
     * @package ResanetSiteBundle
     * @subpackage Controller 
     */
namespace Resanet\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Resanet\MainBundle\Entity\Reservation;
use Resanet\MainBundle\Entity\Client;
use Resanet\MainBundle\Entity\ReservationOptionRes;
use Resanet\MainBundle\Entity\OptionRes;
use Resanet\MainBundle\Entity\Coupon;
use Resanet\PaiementBundle\Classes\Token;

class ReservationFrontController extends Controller
{
    
    /** 
     * renvoie la page de réservation
     * @return view
     */
    
    public function reservationFormAction()
    {
        $em=$this->getDoctrine()->getEntityManager();
        $options=$em->getRepository('ResanetMainBundle:OptionRes')->findAll();
        $token=Token::generateToken($this->getRequest()->getSession()->getId());
        $param=array('options'=>$options, 'token'=>$token);
        return $this->render('ResanetSiteBundle:Reservation:reservationForm.html.twig',$param);
    }
    
    /** 
     * renvoie la page de confirmation de réservation
     * @return view
     */
    
    public function reservationConfirmationAction($resa)
    {
        return $this->render('ResanetSiteBundle:Reservation:reservationConfirmed.html.twig',array('resa'=>$resa));
    }
    
    /** 
     * renvoie la page de réservation avec message erreur
     * @return view
     */
    
    public function reservationErreurFormAction($erreur=null)
    {
        $em=$this->getDoctrine()->getEntityManager();
        if (!$erreur) $erreur="Fin de la transaction";
        $options=$em->getRepository('ResanetMainBundle:OptionRes')->findAll();
        $param=array('options'=>$options, 'erreur'=>$erreur, 'token'=>Token::generateToken($this->getRequest()->getSession()->getId()));
        return $this->render('ResanetSiteBundle:Reservation:reservationForm.html.twig',$param);
    }
    
     /**
     * renvoie un tableau en json des catégorie et du prix pour la période
      * @param string, string (2 dates yyyy-mm-dd)
     * @return json_encode(array)
     */
    
    public function nbChambresCategoriesAction()
    {   $request=$this->getRequest();
        $dateArrivee=$request->get('dateArrivee');
        $dateDepart=$request->get('dateDepart');
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $tabCategorie=array();
        foreach ($categories as $cat){
            $nbChambres=$cat->nbChambresDispo($dateArrivee,$dateDepart);
            if ($nbChambres!=0){
                $prix=$cat->calculPrixPer($dateArrivee, $dateDepart, null);
                $tabCategorie[]=array('prixCategorie'=>$prix,'nomCategorie'=>$cat->getNom(), 'descriptionCategorie'=>$cat->getDescription(), 'nbChambresCategorie'=>$nbChambres, 'idCategorie'=>$cat->getId(), 'imageCategorie'=>$cat->getImage());
            }
        }
        return new Response(json_encode($tabCategorie));
    }
    
    /**
     * vérifie si un coupon est valide
      * @param string, string (2 dates yyyy-mm-dd)
     * @return renvoie au coupon controller
     */
     public function verifCouponFrontEndAction(){
        $codepromo=$this->getRequest()->get('codepromo');
        $date=$this->getRequest()->get('date');
         return $this->forward('ResanetMainBundle:Coupon:verifieCoupon', array('codepromo'=>$codepromo, 'date'=>$date));
    }
    
    
    /** 
     * enregistre la requête
      * @param request
     * @todo ajouter transaction
     * @return 
     */
    public function reservationEnregistrerAction(){
       $validator = $this->get('validator');
       $request=$this->getRequest();
       if (!(Token::isCsrfTokenValid($request->getSession()->getId(), $request->get('token')))){
           $erreur='C\'est mal ce que vous faites.';
          return $this->forward('ResanetSiteBundle:ReservationFront:reservationErreurForm', array('erreur'=>$erreur));
       }
       $em=$this->getDoctrine()->getEntityManager();
      
       $erreur=array();
       $em->getConnection()->beginTransaction();
       try {
            
            $resa=new Reservation();
            //ajoute la date de création et d'option avec la date d'aujourd'hui
            $resa->setDateCreation(new \DateTime(date('Y-m-d')));
            $resa->setDateOption(new \DateTime(date('Y-m-d')));
            //ajoute l'état en option en déterminant sa date jusqu'à laquelle il est maintenu en option sans paiement
             $resa->setEtat('Annulée');
             
               //creation du client
             $client=new Client();
             $client->setNom($request->get('nomClient'));
             $client->setPrenom($request->get('prenomClient'));
             $client->setAdresse($request->get('adresseClient'));
             $client->setCp($request->get('cpClient'));
             $client->setVille($request->get('villeClient'));
             $client->setPays($request->get('paysClient'));
             $client->setEmail($request->get('emailClient'));
             $client->setTelephone($request->get('telephoneClient'));
             $client->setSociete($request->get('societeClient'));
             $erreurClient=$validator->validate($client);
             
           if (count($erreurClient) > 0) {
               foreach ($erreurClient as $err){
                $erreur[]=$err->getMessage();
               }
            } 
            
            $em->persist($client);
            $em->flush();
            $resa->setClient($client);
            $em->persist($resa);
                //date Arrivee
               $dateArrivee=date_create_from_format('d#m#Y', $request->get('dateArrivee'));            
               if(!$dateArrivee)$erreur[]='La date d\'arrivée n\'est pas au format jj/mm/aaaa';
               else {  
                   $dateArrivee->setTime(01,00);
                   $resa->setDateArrivee($dateArrivee);
               }
               //date Depart
               $dateDepart=date_create_from_format('d#m#Y', $request->get('dateDepart'));            
               if(!$dateDepart)$erreur[]='La date de départ n\'est pas au format jj/mm/aaaa';
               else  {
                   $dateDepart->setTime(01,00);
                   $resa->setDateDepart($dateDepart);
               }
              
               //vérifier la dispo des prix et des chambres et ajoute à l'objet Réservation
               
               $nbCategories=$request->get('nombreCategorie');
              
               if($nbCategories){
                   //pour chacune des catégories, on récupère la chambre et on l'attribue
                   // on boucle tant que 
                       foreach( $nbCategories as $id=>$nb ){
                           if ($nb>0){
                               
                               $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
                               if ($categorie){
                                   //recherche chambre libre pour atteindre le niveua requis
                                   $nbChbres=0;
                                   foreach ($categorie->getChambres() as $chambre){
                                       if ($chambre->estDispoPeriode($dateArrivee->format('Y-m-d'), $dateDepart->format('Y-m-d'))){
                                           $resa->addChambre($chambre);
                                           $nbChbres++;
                                       }
                                       if ($nbChbres==$nb) break;
                                    } 
                                    if ($nbChbres!=$nb) $erreur[]='Des chambres ne sont plus disponibles';
                               } else {
                                   $erreur[]='La catégorie donnée n\'existe pas';
                               }
                           }
                       }
               } else {
                   $erreur[]='Aucune chambre n\a été sélectionnée';
               }
               
               //ajoute le coupon et vérifie si son utilisation est correcte ou non
               $couponCodePromo=$request->get('codepromo');
               if ($couponCodePromo){
                   $coupon=$em->getRepository('ResanetMainBundle:Coupon')->findOneBy(array('codePromo'=>$couponCodePromo));
                   if (($coupon)&&($coupon->estValide($resa->getDateCreation()))){
                       $resa->setCoupon($coupon);
                      } 
               }
              

               //s'il y a un commentaire
               $commentaire=$request->get('commentaires');
               if($commentaire){
                  $resa->setCommentaires($commentaire);
               }
               //teste la validité de l'objet Réservation
               $erreurResa=$validator->validate($resa);
               if (count($erreurResa) > 0) {
                   foreach ($erreurResa as $err){
                    $erreur[]=$err->getMessage();
                   }
                }
               $em->flush();
                //s'il y a des options
               $option=$request->get('nombreOption');
               //s'il y a une option on procède à la création des objets
                if($option){
                   foreach($option as $key=>$value){
                       if ($value>0){
                           $reservationOptionRes=new ReservationOptionRes();   
                            //recherche l'option correspondante
                           $optionRes=$em->getRepository('ResanetMainBundle:OptionRes')->find($key);

                           if (is_null($optionRes)==false){
                              $reservationOptionRes->setOptionRes($optionRes);  
                            } else {
                                $erreur[]='Aucune option ne correspond à celle transmise';
                            }
                            $reservationOptionRes->setQuantite($value);               


                            //test de la validité de l'objet paiement
                            $erreurOption=$validator->validate($reservationOptionRes);
                            if (count($erreurOption) > 0) {
                                foreach ($erreurOption as $err){
                                $erreur[]=$err->getMessage();
                                } 
                            } else {
                                // l'ajoute à la réservation
                                $reservationOptionRes->setReservation($resa);
                                $resa->addReservationOptionRes($reservationOptionRes);
                                $em->persist($reservationOptionRes);
                                $em->flush();
                            }
                        }   
                   }
               }
              
                if (count($erreur)>0){
                    
                   $em->getConnection()->rollback();
                   $em->close();
                    return new Response(var_dump($erreur));
                } else {
                    
                    
                    $em->getConnection()->commit();
                    return $this->forward('ResanetPaiementBundle:Default:getToken', array('reservation'=>$resa));
                }
      } catch (\Exception $e) {
          
               $em->getConnection()->rollback();
               $em->close();
               $erreur[]='Veuillez nous signaler l\'erreur svp';
               return new Response(var_dump($erreur));
           }          
}
    
}
