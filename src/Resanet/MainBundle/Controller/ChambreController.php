<?php
    /**
     * @author:Stephane Lanjard stephane.lanjard@gmail.com
     * @package ResanetMainBundle
     * @subpackage Controller 
     */
namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\ChambreType;
use Resanet\MainBundle\Entity\Chambre;
use Symfony\Component\HttpFoundation\Response;


class ChambreController extends Controller
{
    /** 
     * retourne la vue montrant la liste des chambres en json
     *  @return view
     * 
     */
    public function listeChambreAction(){
        $parametres=array();
        $parametres['parametresJson']=array("id","nom","etage","categorie");
        $parametres['parametresTab']=array("n°","Nom","Etage","Categorie");
        $parametres['titre']='Liste des chambres';
        $parametres['nom']='Chambre';
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
     /** 
     * retourne la liste des chambres en json
     *  @return array
     */
    public function listeChambreJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $chambres=$em->getRepository('ResanetMainBundle:Chambre')->findAll();
        $chambres2=array();
        foreach($chambres as $value){
            $chambres2[]=$value->toArray();
        }
        $retour=array('aaData'=>$chambres2);
        return new Response(json_encode($retour));
    }
    
    /**
     * reception des paramètres d'un formulaire et enregistre les détails du chambres
     *  @return array
     */
    
    
    public function enregistrerChambreAction(){
        //test si ajout ou edition d'un chambre par la présence d'une id dans la requete
        $id=is_null($this->getRequest()->get('id')) ? null : $this->getRequest()->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        if (isset($id)){     
            $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($id);
         } else {
            $chambre  = new Chambre();
            } 
            // correspondance entre requete et form
        $request = $this->getRequest();
        $form    = $this->createForm(new ChambreType(), $chambre);
        $form->bindRequest($request);
        //test de validité de l'objet
         if ($form->isValid()) {
            try {
               $em->persist($chambre);
                $em->flush(); 
                $rep = 1;
                $reponse = array("rep" => $rep);
                return new Response(json_encode($reponse), 200);
            } catch (\PDOException $err){
                $error=new \Symfony\Component\Form\FormError('Le nom de la chambre doit être unique');
                $form->addError($error);
            }
        } 
            $engine = $this->container->get('templating');
            $render = $engine->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
                'entity' => $chambre,
                'form'   => $form->createView()
            ));
            //si non valide retour 0 et formulaire pré rempli
            $rep = 0;
            $reponse = array("rep" => $rep, "retour" => $render);
            return new Response(json_encode($reponse), 200);
        
        }
    
    /**
     * supprime le chambre correspondant à l'$id
     *  @return array
     */
    
    public function supprimerChambreAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($id);
        if (!$chambre || $chambre->getReservationChambres()->count()>0) {
         return new Response(json_encode(0));
        } else {
           $em->remove($chambre);
           $em->flush();
           return new Response(json_encode(1)); 
        }
    }
    /**
     * renvoie un formulaire de suppression
     * @todo ajouter condition seulement pour reservation non soldée 
     * @return view
     */
     
    public function supprimerFormChambreAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($id);
        if (!$chambre) {
         return new Response('Aucune chambre correspondante');
        } else {
            $parametres=array('entity'=>$chambre);
            if ($chambre->getReservationChambres()->count()>0){
                $parametres['message']='Il apparait que la chambre est utilisé dans des réservations. Supprimez les réservations attenantes puis supprimer la chambre';
            }
          return $this->render('ResanetMainBundle:Formulaire:supprFormulaire.html.twig', $parametres); 

        }  
        
    }
    
    /**
     * renvoie un formulaire d'ajout
     * @return view
     */
    
    public function ajouterChambreAction(){
        $chambre = new Chambre();
        $form   = $this->createForm(new ChambreType(), $chambre);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $chambre,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * renvoie un formulaire de modification
     * @return view
     */
    
    
    public function updateChambreAction(){
        $id=$this->getRequest()->get('id');
        $em=$this->getDoctrine()->getEntityManager();
        $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($id);
        $form   = $this->createForm(new ChambreType(), $chambre);
        return $this->render('ResanetMainBundle:Formulaire:editionFormulaire.html.twig', array(
            'entity' => $chambre,
            'form'   => $form->createView()
        ));
        
    }
    
    
}
