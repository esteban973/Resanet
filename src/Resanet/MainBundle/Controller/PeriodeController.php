<?php

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\PeriodeType;
use Resanet\MainBundle\Entity\Periode;
use Resanet\MainBundle\Entity\PeriodeCategorie;
use Symfony\Component\HttpFoundation\Response;
use Resanet\MainBundle\Fonctions\CalculDates;


class PeriodeController extends Controller
{
    //liste de tous les periodes
public function listePeriodeAction(){
       $parametres=array();
       $em=$this->getDoctrine()->getEntityManager();
       $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
       $tab=array("id","dateDebut","dateFin");
       $tab2=array("n°","Période de début","Période de fin");
       foreach($categories as $cat){
            $tab[]=$cat->getId();
            $tab2[]=$cat->getNom();
        }
        
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',array('parametresJson'=>$tab,'parametresTab'=>$tab2, 'titre'=>'Liste des périodes','nom'=>'Periode'));
}
    
   //communique en json les périodes et alimente le tableau de affichageListe.html.twig    
    public function listePeriodeJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $periodes=$em->getRepository('ResanetMainBundle:Periode')->findAll();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $periodes2=array();
        foreach($periodes as $value){
            $periodes2[]=$value->toArray($categories);
        }
        $retour=array('aaData'=>$periodes2);
        return new Response(json_encode($retour));
    }
    
    
    
    //envoi des paramètres d'un formulaire et enregistre les détails du periodes
    public function enregistrerPeriodeAction(){
        $erreur=array();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
                
        //ajout ou édition d'une période
        $id=is_null( $request->get('id')) ? null :  $request->get('id');
        $periode=(isset($id))? $em->getRepository('ResanetMainBundle:Periode')->find($id): new Periode();
        
        //transformation des dates pour les mettre au format Datetime et renvoie une erreur si pas bon format
        try{
           $periode->setDateDebut(new \DateTime(CalculDates::transformationDate($request->get('dateDebut'))));
           $periode->setDateFin(new \DateTime(CalculDates::transformationDate($request->get('dateFin'))));
            } catch (\Exception $e){
            $erreur[]='Les dates ne sont pas au format jj/mm/aaaa';
            return $this->retournerErreurAction($periode, $erreur); 
        }
        
        // test si période à cheval avec d'autres périodes
        $periodes=$em->getRepository('ResanetMainBundle:Periode')->tableauPeriode($id); 
        if (\Resanet\MainBundle\Fonctions\CalculDates::aCheval($periode->getDateDebut(),$periode->getDateFin(), $periodes)==false){
            $erreur[]='Les dates sont à cheval avec une autre période';
            return $this->retournerErreurAction($periode, $erreur); 
        }
        
        //test pour vérifier si la période en question a déjà des réservations en cours dans la catégorie de prix
        //s'il y a une réservation ou la période en cours cela bloque l'enregistrement
       $dateJour=new \DateTime(date('Y-m-d'));
        if (($periode->getDateDebut()<=$dateJour)&&($periode->getDateFin()>=$dateJour)){
            $erreur[]='Vous ne pouvez pas changer une période en cours';
            $this->retournerErreurAction($periode, $erreur); 
        }    
        
        //utilise le validateur de Symfony et retourne les erreurs à la vue s'il ya.
        //S'il y en a pas persiste l'objet Période
        $validator = $this->get('validator');
        $erreurPer=$validator->validate($periode);
        if (count($erreurPer) > 0) {
            foreach ($erreurPer as $err){
            $erreur[]=$err->getMessage();
           }
            $this->retournerErreurAction($periode, $erreur); 
        } else { 
            if (is_null($id)){
                $em->persist($periode);
                $em->flush();
            } 
        }
        /*
         * une fois les périodes crées, associons les catégories à la période créé
         * pour toutes les associations prix id Catégorie renvoyé par le formulaire
         * si la périodeCatégorie a une catégorie dans la collection de PeriodeCategorie de Periode
         * alors on teste si c'est un update ou un delete
         * si la periode n'appartient pas à la collection alors on la rajoute
         */
       foreach ($request->get('prix') as $key => $value){
               $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($key);
               //vérifie si la catégorie est déjà définie en utilisant un tableau d'Id renvoyé par l'objet Période
               if (in_array($categorie->getId(), $periode->getIdCat())){
                  foreach($periode->getPeriodeCategories() as $periodeCat){
                      if($periodeCat->getCategorie()->getId()== $categorie->getId()){
                          $periodeCategorie=$periodeCat;
                      }   
                   }
                   // si une période a des réservations en cours on ne peut pas la modifier
                   
                   foreach ($categorie->getChambres() as $chambre){
                       if ($chambre->pasDeReservationPeriode($periode->getDateDebut()->format('Y-m-d'), $periode->getDateFin()->format('Y-m-d'))==false){
                           $erreur[]='Une réservation est en cours sur cette période. Vous ne pouvez pas la modifier';
                           return $this->retournerErreurAction($periode, $erreur); 
                       }
                       
                   }
                       
                       // si le prix est défini, modifie le prix
                       if ($value!=''){
                           $periodeCategorie->setPrix($value);
                       // sinon la retire de la collection
                       } else {
                           $em->remove($periodeCategorie);
                       }
                   // si l'objet n'est pas défini
               } else {
                   if ($value!=''){
                   $periodeCategorie=new PeriodeCategorie();
                   $periodeCategorie->setCategorie($categorie);
                   $periodeCategorie->setPeriode($periode);
                   $periodeCategorie->setPrix($value);
                   $em->persist($periodeCategorie);
                   }
               }
                $erreurPerCat=$validator->validate($periodeCategorie);
                 if (count($erreurPerCat) > 0) {
                    foreach ($erreurPerCat as $err){
                    $erreur[]=$err->getMessage();
                    }
               }
       }
       if (count($erreur) >0) {
            return $this->retournerErreurAction($periode, $erreur);      
         } else {
           $em->flush();
            $rep = 1;
            $reponse = array("rep" => $rep);
            return new Response(json_encode($reponse), 200);
        }  
}

//function qui retourne le formulaire empli avec les erreurs    
public function retournerErreurAction($periode, $erreur){   
    $em = $this->getDoctrine()->getEntityManager();
        $engine = $this->container->get('templating');
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $render = $engine->render('ResanetMainBundle:Formulaire:periodeFormulaire.html.twig', array(
                'periode' => $periode,
                'categories'=>$categories,
                'erreurs'=>$erreur
            ));
         $reponse = array("rep" => 0, "retour" => $render);
         return new Response(json_encode($reponse), 200);     
    }    
    
    
    //supprime le periode correspondant à l'$id
    public function supprimerPeriodeAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $periode=$em->getRepository('ResanetMainBundle:Periode')->find($id);
        if (!$periode) {
         return new Response(json_encode(0));
        } else {
           $em->remove($periode);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormPeriodeAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $periode=$em->getRepository('ResanetMainBundle:Periode')->find($id);
        if (!$periode) {
         return new Response('Aucune periode correspondante');
        } else {
            $parametres=array('entity'=>$periode);
            return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
        }  
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterPeriodeAction(){
        $periode = new Periode();
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $form   = $this->createForm(new PeriodeType(), $periode);
        return $this->render('ResanetMainBundle:Formulaire:periodeFormulaire.html.twig', array(
            'periode' => $periode,
            'categories'   => $categories
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updatePeriodeAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $periode=$em->getRepository('ResanetMainBundle:Periode')->find($id);
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $form   = $this->createForm(new PeriodeType(), $periode);
        return $this->render('ResanetMainBundle:Formulaire:periodeFormulaire.html.twig', array(
            'periode' => $periode,
            'categories'   => $categories
        )); 
    } 
}
