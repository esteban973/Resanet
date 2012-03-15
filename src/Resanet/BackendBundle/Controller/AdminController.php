<?php

namespace Resanet\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{

    public function accueilAction(){
        return $this->render('ResanetBackendBundle:Admin:accueil.html.twig');
    }
   
    
    public function parametresAction(){
        
        return $this->render('ResanetBackendBundle:Admin:parametres.html.twig');
        
    }

    
    public function planningAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $dateDebut=new\DateTime(date("Y-m-d"));
        $dateFin=new\DateTime(date("Y-m-d"));
        $dateFin=$dateFin->add(new \DateInterval('P15D'));
         return $this->render('ResanetPlanningBundle:Default:index.html.twig', array('categories'=>$categories, 'dateDebut'=>$dateDebut, 'dateFin'=>$dateFin));
        
    }
}
?>
