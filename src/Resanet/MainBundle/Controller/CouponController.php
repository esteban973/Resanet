<?php

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\CouponType;
use Resanet\MainBundle\Entity\Coupon;
use Symfony\Component\HttpFoundation\Response;


class CouponController extends Controller
{
    //liste de tous les coupons
    public function listeCouponAction(){
        $parametres=array();
        $parametres['parametresJson']=array("id","codepromo" ,"montant" ,"pourcentage" ,"dateDebut" ,"dateFin" ,"nbDebut" ,"nbUtilise" );
        $parametres['parametresTab']=array("n°","Code promo" ,"Montant" ,"Pourcentage" ,"Date de début" ,"Date de fin" ,"Nb initial" ,"Nb utilisé");
        $parametres['titre']='Liste des coupons de réduction';
        $parametres['nom']='Coupon';
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
       
    public function listeCouponJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $coupons=$em->getRepository('ResanetMainBundle:Coupon')->findAll();
        $coupons2=array();
        foreach($coupons as $value){
            $coupons2[]=$value->toArray();
        }
        $retour=array('aaData'=>$coupons2);
        return new Response(json_encode($retour));
    }
    
    public function verifieCouponAction(){
        $codepromo=$this->getRequest()->get('codepromo');
        $dateEffet=new \DateTime($this->getRequest()->get('date'));
        $em=$this->getDoctrine()->getEntityManager();
        $coupon=$em->getRepository('ResanetMainBundle:Coupon')->findOneBy(array('codePromo'=>$codepromo));
        if (!$coupon) {
          return new Response(json_encode(array('rep'=>0, 'message'=>'Pas de code promo ne correspond au code '.$codepromo)));
        }
        if (!$coupon->estValide($dateEffet)) {
          return new Response(json_encode(array('rep'=>0, 'message'=>'Le code promo '.$codepromo.' est valable pour la période du '.$coupon->getDateDebut()->format('d-m-Y').' au '.$coupon->getDateFin()->format('d-m-Y'))));
        }
        return new Response(json_encode(array('rep'=>1, 'pourcent'=>$coupon->getPourcentage(), 'montant'=>$coupon->getMontant())));
       
    }
    
    
    
    
    //envoi des paramètres d'un formulaire et enregistre les détails du coupons
    public function enregistrerCouponAction(){
        //test si ajout ou edition d'un coupon par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $coupon=$em->getRepository('ResanetMainBundle:Coupon')->find($id);
         } else {
            $coupon  = new Coupon();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new CouponType(), $coupon);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
            try {
               $em->persist($coupon);
                $em->flush(); 
                return new Response(json_encode(array("rep" => 1)), 200);
            } catch (\PDOException $err){
                $error=new \Symfony\Component\Form\FormError('Le nom du code promo doit être unique');
                $form->addError($error);
            }
        } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $coupon,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
        
    //supprime le coupon correspondant à l'$id
    public function supprimerCouponAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $coupon=$em->getRepository('ResanetMainBundle:Coupon')->find($id);
        if (!$coupon || $coupon->getReservations()->count()>0) {
         return new Response(json_encode(0));
        } else {
           $em->remove($coupon);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormCouponAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $coupon=$em->getRepository('ResanetMainBundle:Coupon')->find($id);
    
        if (!$coupon) {
         return new Response('Aucun coupon correspondant');
        } else {
            $parametres=array('entity'=>$coupon);
            if ($coupon->getReservations()->count()>0){
                $parametres['message']='Il apparait que le coupon est utilisé dans des réservations. Supprimez les réservations attenantes puis supprimer le coupon';
            }
          return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
           
        }  
        
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterCouponAction(){
        $coupon = new Coupon();
        $form   = $this->createForm(new CouponType(), $coupon);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $coupon,
            'form'   => $form->createView()
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updateCouponAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $coupon=$em->getRepository('ResanetMainBundle:Coupon')->find($id);
        $form   = $this->createForm(new CouponType(), $coupon);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $coupon,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
