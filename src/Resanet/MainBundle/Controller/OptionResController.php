<?php

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\OptionResType;
use Resanet\MainBundle\Entity\OptionRes;
use Symfony\Component\HttpFoundation\Response;


class OptionResController extends Controller
{
    //liste de tous les options
    public function listeOptionResAction(){
       $parametres=array();
       $parametres['parametresJson']=array("id","nom","description","prix");
       $parametres['parametresTab']=array("n°","Nom","Description","Prix");
       $parametres['titre']='Liste des options';
       $parametres['nom']='OptionRes';
       return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
       
    public function listeOptionResJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $options=$em->getRepository('ResanetMainBundle:OptionRes')->findAll();
        $options2=array();
        foreach($options as $value){
            $options2[]=$value->toArray();
        }
        $retour=array('aaData'=>$options2);
        return new Response(json_encode($retour));
    }
    
    
    
    //envoi des paramètres d'un formulaire et enregistre les détails du options
    public function enregistrerOptionResAction(){
        //test si ajout ou edition d'un option par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $option=$em->getRepository('ResanetMainBundle:OptionRes')->find($id);
         } else {
            $option  = new OptionRes();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new OptionResType(), $option);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
            $em->persist($option);
                $em->flush(); 
                $rep = 1;
                $reponse = array("rep" => $rep);
                return new Response(json_encode($reponse), 200);              
        } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $option,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
        
    //supprime le option correspondant à l'$id
    public function supprimerOptionResAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $option=$em->getRepository('ResanetMainBundle:option')->find($id);
        if (!$option || $option->getReservationOptionRes()->count()>0) {
         return new Response(json_encode(0));
        } else {
           $em->remove($option);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormOptionResAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $option=$em->getRepository('ResanetMainBundle:OptionRes')->find($id);
                
        if (!$option) {
         return new Response('Aucune option correspondant');
        } else {
            $parametres=array('entity'=>$option);
            if ($option->getReservationOptionRes()->count()>0){
                $parametres['message']='Il apparait que l\'option est utilisée dans des réservations. Supprimez les réservations attenantes puis supprimer l\'option';
            }
          return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
 
           
        }  
        
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterOptionResAction(){
        $option = new OptionRes();
        $form   = $this->createForm(new OptionResType(), $option);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $option,
            'form'   => $form->createView()
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updateOptionResAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $option=$em->getRepository('ResanetMainBundle:OptionRes')->find($id);
        $form   = $this->createForm(new OptionResType(), $option);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $option,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
