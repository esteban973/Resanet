<?php

namespace Resanet\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $dateDebut=new\DateTime(date("Y-m-d"));
        $dateFin=new\DateTime(date("Y-m-d"));
        $dateFin=$dateFin->add(new \DateInterval('P15D'));
        return $this->render('ResanetStatBundle:Default:index.html.twig', array('categories'=>$categories, 'dateDebut'=>$dateDebut, 'dateFin'=>$dateFin));
        
    }
    
    function majAction() 
    {
        
        $mois = array ('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
        
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        
        //on récupère les paramètres de la requête ajax
        $date1=$request->get('dateDebut');
        $date2=$request->get('dateFin');
        $catId=$request->get('catId');
        $type=$request->get('type');
        
        //$chambres = tableau contenant toutes les chambres
        if ($catId==0){
           $chambres=$em->getRepository('ResanetMainBundle:Chambre')->findAll();
           } else {
           $chambres=$em->getRepository('ResanetMainBundle:Chambre')->chercheChbreCategorie($catId);
        }
        
        //$categories= tableau contenant toutes les categories
        $categories = $em->getRepository('ResanetMainBundle:Categorie')->findAll();
        
        //on crée 2 objets dateTime
        $d1=new \DateTime($date1);
        $d2=new \DateTime($date2);
        
        //les tableaux
        $tabLegende=array();//contiendra les noms des séries de données
        $tabDate = array();// ['01-01-2012',50] ou [06,50] pour les case 1 et 2
        $tabReponseOcc = array();// le tableau à renvoyer en json [ ['date','taux'], ['01-01-2012',50],['02-01-2012',50],...]
        $tabReponseCa[0] = array('Mois','CA');
        $tabCa = array();
        
       // $moyNuitees = calculMoyNuitees($d1,$d2);
        
        //en fonction du type d'affichage souhaité (par mois(0),par semaine(1),par jour(2) )
        switch($type) {
            
            case 0://par mois
                //le tableau contenant la légende
                $tabLegende[0] = 'Mois';
                $tabLegende[1] = 'Total';
                foreach ($categories as $cat) {
                    $tabLegende[] = $cat->getNom();
                }
                $tabReponseOcc[0] = $tabLegende;
                
                
                while ($d1<=$d2) {
                    $tabCat = array(0,0,0,0,0,0);
                    unset ($tabCa);
                    $m1 = date('m', $d1->getTimestamp());
                    $tabCat[0] = $m1;
                    $nb = 0;
                    $nbResTotal = 0;
                    $caMois = 0;
                    
                    
                    while ( (date('m', $d1->getTimestamp()) == $m1) && ($d1<=$d2) ) {
                        foreach ($categories as $cat) {
                            $id = $cat->getId()+1;
                            $nbParCat = 0;
                            $chambres = $cat->getChambres();
                            $nbChambres = count($chambres);
                            foreach ($chambres as $chambre) {
                                if ($chambre->pasDIndispo($d1)) {
                                    $nb++;
                                    if ( !($chambre->pasDeReservation($d1)) ) {
                                        //occupation
                                        $nbParCat++;
                                        $nbResTotal++;
                                        //ca
                                        $caMois = $caMois + $this->calculCa($chambre, $d1);
                                    }
                                }
                            }
                            $tabCat[$id] = $tabCat[$id]+$nbParCat;
                        }
                        $y1 = date('Y', $d1->getTimestamp());
                        $d1=$d1->add(new \DateInterval('P1D'));
                    }
                    //occupation
                    $tabCat[1] = $nbResTotal;
                    $tabReponseOcc[] =$tabCat;
                    
                    //ca
                    $tabCa[] = $mois[$m1-1].' '.$y1;
                    $tabCa[] = $caMois;
                    $tabReponseCa[] = $tabCa;
                }
                break;
           
            case 1://par semaine
                $tabLegende[0] = 'Semaine';
                $tabLegende[1] = 'Taux';
                $tabReponseOcc[] = $tabLegende;
                
                while ($d1<=$d2) {
                    unset($tabDate);//on vide $tabDate
                    unset ($tabCa);
                    $w1 = date('W', $d1->getTimestamp());//n° de semaine de $d1
                    $tabDate[0] = $w1;
                    $nb = 0;
                    $occ = 0;
                    $caSem = 0;
                    
                    while ( (date('W', $d1->getTimestamp()) == $w1) && ($d1<=$d2) ) {
                        foreach ($chambres as $chambre) {
                            if ($chambre->pasDIndispo($d1)) {
                                $nb++;//le nombre total de chambres est le nombre de chambres disponibles
                                if ( ! ($chambre->pasDeReservation($d1))) {
                                        $occ++;
                                        //ca
                                        $caSem = $caSem+ $this->calculCa($chambre, $d1);
                                }
                            }
                        }
                        $d1=$d1->add(new \DateInterval('P1D'));
                    }
                    
                    $tx = ($nb==0 ? 0 : ( (round( ($occ/$nb), 4) )));
                    $tabDate[] = $tx;
                    $tabReponseOcc[] =  $tabDate;

                    //ca
                    $tabCa[] = $w1;
                    $tabCa[] = $caSem;
                    $tabReponseCa[] = $tabCa;
                }
                break;
                
            case 2://par jour
                $tabLegende[0] = 'Date';
                $tabLegende[1] = 'Taux';
                $tabReponseOcc[] = $tabLegende;
                
                while ($d1<=$d2) {
                    unset($tabDate);//on vide $tabDate
                    unset ($tabCa);
                    $tabDate[0] = $d1->format('d-m-Y');
                    $occ = 0;//nombre de réservations
                    $nb = 0;//nombre total de chambres
                    $caJour = 0;
                    foreach ($chambres as $chambre) {
                        if ($chambre->pasDIndispo($d1)) {
                            $nb++;//le nombre total de chambres est le nombre de chambres disponibles
                            if ( ! ($chambre->pasDeReservation($d1))) {
                                    $occ++;
                                    //ca
                                    $caJour = $caJour+ $this->calculCa($chambre, $d1);
                            }
                        }
                    }
                    //occupation
                    $tx = ($nb==0 ? 0 : ( (round( ($occ/$nb), 4) )));
                    $tabDate[] = $tx;
                    $tabReponseOcc[] = $tabDate;
                    
                    //ca
                    $tabCa[] = $d1->format('d-m-Y');
                    $tabCa[] = $caJour;
                    $tabReponseCa[] = $tabCa;

                    $d1=$d1->add(new \DateInterval('P1D'));
                }
                break;
            }
            
        $tabReponse ['occupation'] = $tabReponseOcc;
        $tabReponse ['ca'] = $tabReponseCa;
        return new Response(json_encode($tabReponse));
    }
    
    function calculCa($chambre,$d1) {
        $cat=$chambre->getCategorie();
        $res = $chambre->correspondReservation($d1);
        $dateCreation=$res->getDateCreation();
        $prixJournalier=$cat->calculPrix($d1, $dateCreation);
        return  $prixJournalier+ ( ($res->getTotalOptions()) / (($res->getNbNuits())*(count($res->getChambres()))) );
    }
    function calculMoyNuitees($d1,$d2) {
        
    }
}
