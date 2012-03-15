<?php
/**
     * @package ResanetMainBundle
     * @subpackage Controller 
     */
namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\ClientType;
use Resanet\MainBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Response;


class ClientController extends Controller
{
    
    /** 
     * liste de tous les clients
     * @return view
     */
    public function listeClientAction(){
       $parametres=array();
       $parametres['parametresJson']=array("id","nom","societe","telephone","email","ville");
       $parametres['parametresTab']=array("n°","Nom et prénom","Société","Téléphone","Adresse email","Ville et Pays");
       $parametres['titre']='Liste des clients';
       $parametres['nom']='Client';
       return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
    /** 
     * function appellee par la vue Réservation pour afficher la liste des client et pouvoir la modifier et choisir parmi
     * @return PartialView
     */
   
    public function listeClientTableauAction(){
       $parametres=array();
       $parametres['parametresJson']=array("id","nom","societe","telephone","email","ville");
       $parametres['parametresTab']=array("n°","Nom et prénom","Société","Téléphone","Adresse email","Ville et Pays");
       $parametres['titre']='Liste des clients';
       $parametres['nom']='Client';
       return $this->render('ResanetMainBundle:Admin:affichageTableau.html.twig',$parametres);
    }
    
    /** 
     * function appellee par la vue Réservation pour afficher la liste des client et pouvoir la modifier et choisir parmi
     * @return PartialView
     */
    
    public function getInfoClientAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $client=$em->getRepository('ResanetMainBundle:Client')->find($id);
        return new Response(json_encode($client->toArray()));
    }
       
    /** 
     * renvoie la liste de clients en Json
     * @return Json
     */
    public function listeClientJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $clients=$em->getRepository('ResanetMainBundle:Client')->findAll();
        $clients2=array();
        foreach($clients as $value){
            $clients2[]=$value->toArray();
        }
        $retour=array('aaData'=>$clients2);
        return new Response(json_encode($retour));
    }
    
    
    /** 
     * envoi des paramètres d'un formulaire et enregistre les détails du clients
     * @return Json
     */
   
    public function enregistrerClientAction(){
        //test si ajout ou edition d'un client par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $client=$em->getRepository('ResanetMainBundle:Client')->find($id);
         } else {
            $client  = new Client();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new ClientType(), $client);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
                $em->persist($client);
                $em->flush(); 
                return new Response(json_encode(array("rep" => 1)), 200);
         } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $client,
                'form'   => $form->createView()
            ));
             return new Response(json_encode(array("rep" => 0, "retour" => $render)), 200);
        
        }
    
     /** 
     * supprime le client correspondant à l'$id
     * @return Json
     */
    public function supprimerClientAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $client=$em->getRepository('ResanetMainBundle:Client')->find($id);
        if (!$client || $client->getReservations()->count()>0) {
         return new Response(json_encode(0));
        } else {
           $em->remove($client);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     /** 
     * renvoie un formulaire de suppression
     * @return partialView
     */
   
    public function supprimerFormClientAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $client=$em->getRepository('ResanetMainBundle:Client')->find($id);
        if (!$client) {
         return new Response('Aucune client correspondante');
        } else {
            $parametres=array('entity'=>$client);
            if ($client->getReservations()->count()>0){
                $parametres['message']='Il apparait que le client a de nombreuses réservations attenantes. Supprimez les réservations puis supprimez le client.';
            }
          return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
        }   
    }
    
    /** 
     * renvoie un formulaire d'ajout
     * @return partialView
     */
    public function ajouterClientAction(){
        $client = new Client();
        $form   = $this->createForm(new ClientType(), $client);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $client,
            'form'   => $form->createView()
        ));
    }
    
    
    /** 
     * renvoie un formulaire de modification
     * @return partialView
     */
    public function updateClientAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $client=$em->getRepository('ResanetMainBundle:Client')->find($id);
        $form   = $this->createForm(new ClientType(), $client);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $client,
            'form'   => $form->createView()
        ));
    }  
    
}
