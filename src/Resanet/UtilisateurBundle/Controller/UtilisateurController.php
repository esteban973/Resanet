<?php

namespace Resanet\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Resanet\UtilisateurBundle\Form\UtilisateurType;
use Resanet\UtilisateurBundle\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;

class UtilisateurController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('ResanetUtilisateurBundle:Utilisateur:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
        //liste de tous les utilisateurs
    public function listeUtilisateurAction(){
        $parametres=array();
        $parametres['parametresJson']=array("id","identifiant","nomUtilisateur","fonctionUtilisateur");
        $parametres['parametresTab']=array("n°","Identifiant","Nom et Prénom Utilisateur","Fonction");
        $parametres['titre']='Liste des utilisateurs';
        $parametres['nom']='Utilisateur';
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
       
    public function listeUtilisateurJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $utilisateurs=$em->getRepository('ResanetUtilisateurBundle:Utilisateur')->findAll();
        $utilisateurs2=array();
        foreach($utilisateurs as $value){
            $utilisateurs2[]=$value->toArray();
        }
        $retour=array('aaData'=>$utilisateurs2);
        return new Response(json_encode($retour));
    }
    
    /*
     * @todo: ajouter contrainte unique pour catcher pdo exception
     */
    
    //envoi des paramètres d'un formulaire et enregistre les détails du utilisateurs
    public function enregistrerUtilisateurAction(){
        //test si ajout ou edition d'un utilisateur par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $utilisateur=$em->getRepository('ResanetUtilisateurBundle:Utilisateur')->find($id);
         } else {
            $utilisateur  = new Utilisateur();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new UtilisateurType(), $utilisateur);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
            $motPasse=$utilisateur->getPasswordUtilisateur();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($utilisateur);
            $password = $encoder->encodePassword($motPasse, $utilisateur->getSalt());
            $utilisateur->setPasswordUtilisateur($password);
            $em->persist($utilisateur);
             $em->flush(); 
             return new Response(json_encode(array("rep" => 1)), 200);
          } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $utilisateur,
                'form'   => $form->createView()
            ));
            $reponse = array("rep" => 0, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
        
    //supprime le utilisateur correspondant à l'$id
    public function supprimerUtilisateurAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $utilisateur=$em->getRepository('ResanetUtilisateurBundle:Utilisateur')->find($id);
         $em->remove($utilisateur);
          $em->flush();
           return new Response(json_encode(1)); 
        
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormUtilisateurAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $utilisateur=$em->getRepository('ResanetUtilisateurBundle:Utilisateur')->find($id);
        if (!$utilisateur) {
         return new Response('Aucune utilisateur correspondante');
        } else {
            $parametres=array('entity'=>$utilisateur);
             return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 

        }  
        
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterUtilisateurAction(){
        $utilisateur = new Utilisateur();
        $form   = $this->createForm(new UtilisateurType(), $utilisateur);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $utilisateur,
            'form'   => $form->createView()
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updateUtilisateurAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $utilisateur=$em->getRepository('ResanetUtilisateurBundle:Utilisateur')->find($id);
        $form   = $this->createForm(new UtilisateurType(), $utilisateur);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $utilisateur,
            'form'   => $form->createView()
        ));
        
    }
    
    
    
}

?>
