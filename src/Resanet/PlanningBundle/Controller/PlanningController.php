<?php

namespace Resanet\PlanningBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PlanningController extends Controller
{
public function tableauDisponibilitesAction(){
        $request=$this->getRequest();
         $em = $this->getDoctrine()->getEntityManager();
        $date1=$request->get('dateDebut');
        $date2=$request->get('dateFin');
        $catId=is_null($request->get('catId')) ? null : $request->get('catId');
        if (isset($catId)){
           $chambres=$em->getRepository('ResanetMainBundle:Chambre')->chercheChbreCategorie($catId);
           } else {
            $chambres=$em->getRepository('ResanetMainBundle:Chambre')->findAll();
        } 
        $tab=array();
        foreach($chambres as $chambre){
            $tabChambre=array();
            $d1=new \DateTime($date1);
            $d2=new \DateTime($date2);
            while ($d1<=$d2){
                $tabDateChbre=array();
                //déterminer si la chambre est indispo
                if (!$chambre->pasDIndispo($d1)){
                   $tabDateChbre['etat']=1;
                } else {
                    //si la chambre est dispo, déterminer si il y a une réservation
                   if ($chambre->pasDeReservation($d1)){
                        $cat=$chambre->getCategorie();
                        //pas de réservation et un prix définie elle est libre
                        if ($cat->isPrixDef($d1)){
                            $tabDateChbre['etat']=7;
                            $tabDateChbre['prix']=$cat->calculPrix($d1, null);
                        } else {
                            // pas de réservation mais pas de prix défini
                            $tabDateChbre['etat']=2;
                        }
                        // la chambre est réservée
                    } else {
                        $res=$chambre->correspondReservation($d1);
                        $tabDateChbre['nomClient']=$res->getClient()->getNom().' '.$res->getClient()->getPrenom();
                        $tabDateChbre['idReservation']=$res->getId();
                        $tabDateChbre['dateArrivee']=$res->getDateArrivee()->format('d-m-Y');
                        $tabDateChbre['dateDepart']=$res->getDateDepart()->format('d-m-Y');
                        $dateAuj=new\DateTime(date("Y-m-d"));
                        //vérifions si on est supérieur à la date d'aujourd'hui ou pas
                        // si on est dessus on aura le choix entre no-show ou entre occupee
                        switch ($res->getEtat()){
                            case 'Option':
                                $tabDateChbre['etat']=3;
                                break;
                            case 'Confirmée':
                                $tabDateChbre['etat']=4;
                                break;
                            case 'En cours':
                                $tabDateChbre['etat']=5;
                                break;
                            case 'Terminée':
                                $tabDateChbre['etat']=6;
                                break;
                            case 'Soldée':
                                $tabDateChbre['etat']=6;
                                break;
                        }
                    }
                }
                $tabChambre[$d1->format('d-m-Y')]= $tabDateChbre;
                $d1=$d1->add(new \DateInterval('P1D'));   
            } 
            $tab[$chambre->getNom()]=$tabChambre;
        }
        return new Response(json_encode($tab));
}
       
}
?>
