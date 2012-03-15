<?php

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\ReductionType;
use Resanet\MainBundle\Entity\Reduction;
use Symfony\Component\HttpFoundation\Response;


class ReductionController extends Controller
{
    /*
     * //liste de tous les reductions
     * @todo rajouter dans le tableau de réduction les catégories correspondantes
     * @return view
     */
    public function listeReductionAction(){
        $parametres=array();
       $parametres['parametresJson']=array("id","nom","pourcentage","dateDebEff","dateFinEff","dateDebPeriod","dateFinPeriod");
       $parametres['parametresTab']=array("n°","Nom","Remise","Début","Fin", "Début Période","Fin Période");
       $parametres['titre']='Liste des réductions';
       $parametres['nom']='Reduction';
       return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
       
    public function listeReductionJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $reductions=$em->getRepository('ResanetMainBundle:Reduction')->findAll();
        $reductions2=array();
        foreach($reductions as $value){
            $reductions2[]=$value->toArray();
        }
        $retour=array('aaData'=>$reductions2);
        return new Response(json_encode($retour));
    }
    
     /* envoi des paramètres d'un formulaire et enregistre les détails du reductions
      * @todo rajouter la condition qu'une réduction pour une catégorie a une seule période d'effectivité
      * @return json_encode (array(int, html))
      */
    
    
    public function enregistrerReductionAction(){
        //test si ajout ou edition d'un reduction par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $reduction=$em->getRepository('ResanetMainBundle:Reduction')->find($id);
         } else {
            $reduction  = new Reduction();
            } 
        
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new ReductionType(), $reduction);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
                $em->persist($reduction);
                $em->flush(); 
                $rep = 1;
                $reponse = array("rep" => $rep);
                return new Response(json_encode($reponse), 200);
           } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $reduction,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
        
    //supprime le reduction correspondant à l'$id
    public function supprimerReductionAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $reduction=$em->getRepository('ResanetMainBundle:Reduction')->find($id);
        $em->remove($reduction);
        $em->flush();
        return new Response(json_encode(1)); 
        
    }
    
     //renvoie un formulaire d'ajout
    public function supprimerFormReductionAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $reduction=$em->getRepository('ResanetMainBundle:Reduction')->find($id);
        if (!$reduction) {
         return new Response('Aucune reduction correspondante');
        } else {
           $parametres=array('entity'=>$reduction);
           return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 
        }  
        
    }
    
    //renvoie un formulaire d'ajout
    public function ajouterReductionAction(){
        $reduction = new Reduction();
        $form   = $this->createForm(new ReductionType(), $reduction);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $reduction,
            'form'   => $form->createView()
        ));
    }
    
    
    //renvoie un formulaire de modification
    public function updateReductionAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $reduction=$em->getRepository('ResanetMainBundle:Reduction')->find($id);
        $form   = $this->createForm(new ReductionType(), $reduction);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $reduction,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
