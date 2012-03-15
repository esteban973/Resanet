<?php
/**
     * @author:Stephane Lanjard stephane.lanjard@gmail.com
     * @package ResanetMainBundle
     * @subpackage Controller 
     */

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\CategorieType;
use Resanet\MainBundle\Entity\Categorie;
use Symfony\Component\HttpFoundation\Response;


class CategorieController extends Controller
{
    /* 
     * renvoie la vue avec liste de tous les categories
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listeCategorieAction(){
       $parametres=array();
       $parametres['parametresJson']=array("id","nom","description","image");
       $parametres['parametresTab']=array("n°","Nom","Description","Image");
       $parametres['titre']='Liste des catégories';
       $parametres['nom']='Categorie';
       $parametres['nomEntite']='Categorie';
       return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
     /* 
     * renvoie les categories en format Json
     * @return array()
     */  
    public function listeCategorieJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $categories2=array();
        foreach($categories as $value){
            $categories2[]=$value->toArray();
        }
        $retour=array('aaData'=>$categories2);
        return new Response(json_encode($retour));
    }
    
    
     /* 
      * 
     * réception des paramètres d'un formulaire et enregistre les détails du categorie
      *renvoie un tableau en json indiquant la réalisation (appel en ajax)
     * @return array()
     */  
    
    public function enregistrerCategorieAction(){
        //test si ajout ou edition d'un categorie par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
         } else {
            $categorie  = new Categorie();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new CategorieType(), $categorie);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
            try {
               $em->persist($categorie);
                $em->flush(); 
                $rep = 1;
                $reponse = array("rep" => $rep);
                return new Response(json_encode($reponse), 200);
            } catch (\PDOException $err){
                $error=new \Symfony\Component\Form\FormError('Le nom de la categorie doit être unique');
                $form->addError($error);
            }
        } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $categorie,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
     /* 
      * 
     * supprime le categorie correspondant à l'$id
     * @return array()
     */     
    
    public function supprimerCategorieAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
        if (!$categorie || $categorie->getChambres()->count()>0) {
         return new Response(json_encode(0));
        } else {
           $em->remove($categorie);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
   
    /* 
      * 
     * associe une image à une catégorie caractériser par l'id, appel en ajax, renvoie une réponse en ajax
     * @return array()
     */  
    public function imageCategorieAction(){
        $id=$this->getRequest()->get('id');
        $img=$this->getRequest()->get('img');
        $em=$this->getDoctrine()->getEntityManager();
        $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
        if (!$categorie) {
         return new Response(json_encode(0));
        } else {
           $categorie->setImage($img);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    
     /* 
      * 
     * renvoie un formulaire de suppression de categorie, appel en ajax, renvoie une réponse en ajax
     * @return array()
     */ 
     
    public function supprimerFormCategorieAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
        if (!$categorie) {
         return new Response('Aucune categorie correspondante');
        } else {
            $parametres=array('entity'=>$categorie);
            if ($categorie->getChambres()->count()>0){
                $parametres['message']='Il apparait que la categorie a de nombreuses chambres attenantes. Supprimez les chambres puis supprimez la catégorie.';
            }
          return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 

        }  
        
    }
    
    /* 
      * 
     * renvoie un formulaire d'ajout de categorie, appel en ajax, renvoie une réponse en ajax
     * @return array()
     */ 
    
    public function ajouterCategorieAction(){
        $categorie = new Categorie();
        $form   = $this->createForm(new CategorieType(), $categorie);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $categorie,
            'form'   => $form->createView()
        ));
    }
    
    /* 
      * 
     * renvoie un formulaire de modification de categorie, appel en ajax, renvoie une réponse en ajax
     * @return array()
     */ 
    public function updateCategorieAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $categorie=$em->getRepository('ResanetMainBundle:Categorie')->find($id);
        $form   = $this->createForm(new CategorieType(), $categorie);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $categorie,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
