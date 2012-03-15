<?php

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\IndispoType;
use Resanet\MainBundle\Entity\Indispo;
use Symfony\Component\HttpFoundation\Response;


class IndispoController extends Controller
{
    //liste de tous les indispos
    public function listeIndispoAction(){
        $parametres=array();
        $parametres['parametresJson']=array("id","dateDebut","dateFin","chambre");
        $parametres['parametresTab']=array("n°","Date de début","Date de Fin","Chambre");
        $parametres['titre']='Liste des indispos';
        $parametres['nom']='Indispo';
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
       
    public function listeIndispoJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $indispos=$em->getRepository('ResanetMainBundle:Indispo')->findAll();
        $indispos2=array();
        foreach($indispos as $value){
            $indispos2[]=$value->toArray();
        }
        $retour=array('aaData'=>$indispos2);
        return new Response(json_encode($retour));
    }
    
    /*envoi des paramètres d'un formulaire et enregistre les détails du indispos
     * @todo ajouter condition sur la période d'indispo pour vérifier que pas à cheval
     * @return json_encode(array(int, html))
     */

    public function enregistrerIndispoAction(){
        //test si ajout ou edition d'un indispo par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $indispo=$em->getRepository('ResanetMainBundle:Indispo')->find($id);
         } else {
            $indispo  = new Indispo();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new IndispoType(), $indispo);
        $form->bindRequest($request);
        $indispos=$em->getRepository('ResanetMainBundle:Indispo')->tableauIndispo($id,$indispo->getChambre()->getId()); 
        if (\Resanet\MainBundle\Fonctions\CalculDates::aCheval($indispo->getDateDebut(),$indispo->getDateFin(), $indispos)==false){
           $error=new \Symfony\Component\Form\FormError('Les dates sont à cheval avec une autre période pour cette chambre');
            $form->addError($error);
        }
        //test de validité de l'objet
         if ($form->isValid()) {
               $em->persist($indispo);
                $em->flush(); 
                $rep = 1;
                $reponse = array("rep" => $rep);
                return new Response(json_encode($reponse), 200);
        
        } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $indispo,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
        
    //supprime le indispo correspondant à l'$id
    public function supprimerIndispoAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $indispo=$em->getRepository('ResanetMainBundle:Indispo')->find($id);
        if (!$indispo) {
         return new Response(json_encode(0));
        } else {
           $em->remove($indispo);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormIndispoAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $indispo=$em->getRepository('ResanetMainBundle:Indispo')->find($id);
        if (!$indispo) {
         return new Response('Aucune indispo correspondante');
        } else {
            $parametres=array('entity'=>$indispo);
            return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
        }  
        
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterIndispoAction(){
        $indispo = new Indispo();
        $form   = $this->createForm(new IndispoType(), $indispo);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $indispo,
            'form'   => $form->createView()
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updateIndispoAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $indispo=$em->getRepository('ResanetMainBundle:Indispo')->find($id);
        $form   = $this->createForm(new IndispoType(), $indispo);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $indispo,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
