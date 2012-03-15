<?php
    /**
     * @author:Stephane Lanjard stephane.lanjard@gmail.com
     * @package ResanetMainBundle
     * @subpackage Controller 
     */

namespace Resanet\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Resanet\MainBundle\Form\ReservationType;
use Resanet\MainBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Response;
use Resanet\MainBundle\Fonctions\CalculDates;
use Resanet\MainBundle\Entity\Paiement;
use Resanet\MainBundle\Entity\Occupant;
use Resanet\MainBundle\Entity\OptionRes;
use Resanet\MainBundle\Entity\Personne;
use Resanet\MainBundle\Entity\ReservationOptionRes;
use Resanet\MainBundle\Entity\Annulation;

    
class ReservationController extends Controller
{
    /**
     * renvoie une vue donnant la liste des reservations (chargees en Ajax)
     * permet de selectionner la reservation, de la supprimer, de l'editer ou d'en ajouter
     * @link listeReservationJsonAction()
     * @return  Symfony\Component\HttpFoundation\Response listeReservation.html.twig
     */
    
    public function listeReservationAction(){
       $parametres=array();
       $parametres['parametresJson']=array("id","date","dateDebut","dateFin","nomClient", "chambres","etat" ,"solde" ,"total");
       $parametres['parametresTab']=array('n°','Faite le','Début Résa','Fin Résa','Nom du client','Chambres','Etat résa','Solde','Total');
       $parametres['nom']='Reservation';
       $parametres['nomEntite']='Reservation';
       $parametres['titre']='Liste des réservations';
        return $this->render('ResanetMainBundle:Admin:affichageListe.html.twig',$parametres);
    }
    
    /**
     * renvoie un tableau en Json qui permettra d'alimenter la datatable de la vue listeReservation.html.twig
     * est appele en ajax
     * @return array()
     */
       
    public function listeReservationJsonAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $reservations=$em->getRepository('ResanetMainBundle:Reservation')->findAll();
        $reservations2=array();
        foreach($reservations as $value){$reservations2[]=$value->toArray();}
        $retour=array('aaData'=>$reservations2);
        return new Response(json_encode($retour));
    }
    
     /**
     * renvoie un tableau en Json qui permettra d'alimenter le formulaire de creation de reservation 
      * reservationFormulaire.html.twig
     * est appele en ajax, fait appel a ReservationController::
      * @link listeChambresDispoAction()
     * @return array()
     */
    
    public function listeChambreDispoJsonAction(){
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        $date1=$request->get('date1');
        $date2=$request->get('date2');
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $tabChambres=$this->listeChambresDispoAction($date1, $date2, null);
        return new Response(json_encode($tabChambres));
    }
    
    
    /**
     * renvoie un tableau en Json qui permettra d'alimenter le formulaire d'edition de reservation 
      * reservationFormulaire.html.twig
     * est appele en ajax, fait appel a ReservationController::
     * @link listeChambresDispoAction()
     * @return array()
     */
    
    public function listeChambreDispoJsonEditionAction(){
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        $date1=$request->get('date1');
        $date2=$request->get('date2');
        $resaId=$request->get('resaId');
        $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($resaId);
        $tabChambres=$this->listeChambresDispoAction($date1, $date2, null);
        foreach ($resa->getChambres() as $chambre){
             if ($chambre->estDispoPeriodeHormisRes($date1,$date2,$resa)){
                        $tabChambres[]=array('prix'=>$chambre->getCategorie()->calculPrixPer($date1,$date2, $resa->getDateCreation()),
                                            'categorie'=>$chambre->getCategorie()->getNom(), 
                                            'chambre'=>$chambre->getNom(), 
                                            'id'=>$chambre->getId());
                 }
        }
        return new Response(json_encode($tabChambres));
    }
   
    

    /**
     * receptionne la requete de modification de reservation annulee
     * @return array()
     */
    public function enregistrerReservationAnnuleeAction(){
        
       $validator = $this->get('validator');
       $request=$this->getRequest();
       $em=$this->getDoctrine()->getEntityManager();
       $erreur=array();
       $em->getConnection()->beginTransaction();
       try {
                    //on verifie l'objet Reservation
           $id=$request->get('id');
           $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
           if (!$resa){
                   $erreur[]='Aucune reservation ne correspond a la requête';
                   return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
            }
              if ($resa->getEtat()!='Annulée'){
                   $erreur[]='Vous pouvez modifier qu\'une reservation annulee';
                   return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
              }
               //vider toutes les paiements de l'objet Reservation
             
              foreach ($resa->getPaiements() as $paiement){
                       $resa->getPaiements()->removeElement($paiement);
                       $em->remove($paiement);
              }
              
              $em->flush();
           
             
           //s'il y a un paiement
           $paiementTypes=$request->get('paiementType');
           $paiementMontants=$request->get('paiementMontant');
           $paiementDates=$request->get('paiementDate');
           
           //s'il y a un paiement on procede a la creation des objets
            if($paiementTypes){
                
               foreach($paiementTypes as $key=>$value){
                   $paiement=new Paiement();
                    //date Paiement
                   $datePaiement=date_create_from_format('d#m#Y', $paiementDates[$key]);            
                   if(!$datePaiement)$erreur[]='La date de paiement n\'est pas au format jj/mm/aaaa';
                   else  $paiement->setDatePaiement($datePaiement);
                    
                    $paiement->setMontant($paiementMontants[$key]);
                    $paiement->setMoyen($paiementTypes[$key]);
                    //test de la validite de l'objet paiement
                    $erreurPaie=$validator->validate($paiement);
                    if (count($erreurPaie) > 0) {
                        foreach ($erreurPaie as $err){
                            // Librement inspire  du f.anton touch
                            $erreur[]=$err->getMessage();
                        } 
                    } else {
                        // l'ajoute a la reservation
                        $paiement->setReservation($resa);
                        $resa->addPaiement($paiement);
                        $em->persist($paiement);
                    }
                }
                $mont=round(floatval($resa->getAnnulation()->getMontant()),2);
                $tot=round(floatval($resa->getTotalPaye()),2);
                if ($mont==$tot){$resa->setEtat('Soldée');}
                $em->flush();
           }
            if (count($erreur)>0){
               $em->getConnection()->rollback();
               $em->close();
                return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
            } else {
                $em->getConnection()->commit();
                
               return new Response(json_encode(array('rep'=>1, 'etat'=>$resa->getEtat(), 'id'=>$resa->getId())));
            }
       } catch (\Exception $e) {
               $em->getConnection()->rollback();
               $em->close();
               $erreur[]='Veuillez nous signaler l\'erreur svp';
               return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
           }
}

 /**
  * @
     * receptionne la requete de creation de reservation, verifie sa validite et retourne soit 
     * si c'est bon 1 et l'id de la reservation
     * si ce n'est pas bon 0 et un tableau d'erreur
     * @return array()
     */
    public function enregistrerReservationExistanteAction(){
        
       $validator = $this->get('validator');
       $request=$this->getRequest();
       $em=$this->getDoctrine()->getEntityManager();
       $erreur=array();
       $em->getConnection()->beginTransaction();
       try {
           $arrhes=$this->container->getParameter('arrhes');
           
           //on verifie l'objet Reservation
           $id=$request->get('id');
           if ($id){
              $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
               if (!$resa){
                   $erreur[]='Aucune reservation ne correspond a la requête';
                   return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
               }
              if ($resa->getEtat()=='Soldée'){
                   $erreur[]='Vous ne pouvez pas modifier une reservation soldée';
                   return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
              }
               //vider toutes les chambres de l'objet Reservation
              foreach ($resa->getChambres() as $chambre){
                       $resa->getChambres()->removeElement($chambre);
              }
              foreach ($resa->getPaiements() as $paiement){
                       $resa->getPaiements()->removeElement($paiement);
                       $em->remove($paiement);
              }
                foreach ($resa->getReservationOptionRes() as $resOptRes){
                       $resa->getReservationOptionRes()->removeElement($resOptRes);
                       $em->remove($resOptRes);
              }
               foreach ($resa->getOccupants() as $occupant){
                       $resa->getOccupants()->removeElement($occupant);
                       $em->remove($occupant);
              }
              $em->flush();
           } else {
               $resa=new Reservation();
               //ajoute la date de creation avec la date d'aujourd'hui
               $resa->setDateCreation(new \DateTime(date('Y-m-d')));
       
               //ajoute l'etat en option en determinant sa date jusqu'a laquelle il est maintenu en option sans paiement
               $resa->setEtat('Option');
               $em->persist($resa);
           }
           //recherche le client
           $idClient=$request->get('clientId'); 
           $client=$em->getRepository('ResanetMainBundle:Client')->find($idClient);
           if (!$client){
               $erreur[]='Aucun client ne correspond a vôtre requête';
           } 
           $resa->setClient($client);
           //date Option
           $dateOption=date_create_from_format('d#m#Y', $request->get('dateOption'));            
           if(!$dateOption)$erreur[]='La date d\'option ne sont pas au format jj/mm/aaaa';
           else {
               $dateOption->setTime(01,00);
               $resa->setDateOption($dateOption);
           }
           //date Arrivee
           $dateArrivee=date_create_from_format('d#m#Y', $request->get('dateArrivee'));            
           if(!$dateArrivee)$erreur[]='La date d\'arrivee n\'est pas au format jj/mm/aaaa';
           else {  
               $dateArrivee->setTime(01,00);
               $resa->setDateArrivee($dateArrivee);
           }
           //date Depart
           $dateDepart=date_create_from_format('d#m#Y', $request->get('dateDepart'));            
           if(!$dateDepart)$erreur[]='La date de depart n\'est pas au format jj/mm/aaaa';
           else  {
               $dateDepart->setTime(01,00);
               $resa->setDateDepart($dateDepart);
           
           }
           //determine s'il ya checkin les formattent pour le Datetime
            if ($request->get('dateCheckIn')){
                   $date=date_create_from_format('d#m#Y H:i ', $request->get('dateCheckIn'));            
                   if(!$date)$erreur[]='Les horaires de checkIn ne sont pas au format jj/mm/aaaa hh:mm';
                   else  $resa->setDateCheckIn($date);
               } else $resa->setDateCheckIn(null);
            
        //determine s'il ya checkOut les formattent pour le Datetime    
               
               if ($request->get('dateCheckOut')){
                   $date=date_create_from_format('d#m#Y H:i ', $request->get('dateCheckOut'));
                   if(!$date)$erreur[]='Les horaires de CheckOut ne sont pas au format jj/mm/aaaa hh:mm';
                   else $resa->setDateCheckOut($date);
               } else $resa->setDateCheckOut(null);
               
           //heure arrivee
           $heureArrivee=$request->get('heureArrivee');
           if($heureArrivee){
               $resa->setHeureArrivee($heureArrivee);
           } else $resa->setHeureArrivee(null);
           
           //verifier la dispo des prix et des chambres et ajoute a l'objet Reservation
           $chambresId=$request->get('chambre');
           if($chambresId){
                   foreach( $chambresId as $chambreId ){
                       $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($chambreId);
                        
                       if (!$chambre){
                       $erreur[]='Une des chambres ne correspond a vôtre requête';
                        } else {
                            if ($resa->getChambres()->contains($chambre)){
                              $erreur[]='Vous ne pouvez pas reservez 2 fois la même chambre '.$chambre->getNom();  
                            } else {
                               
                                if ($chambre->estDispoPeriode($dateArrivee->format('Y-m-d'), $dateDepart->format('Y-m-d'))){
                                  
                                    $resa->addChambre($chambre);
                                   
                                } else {
                                    $erreur[]='La chambre '.$chambre->getNom().' n\'est plus disponible';
                                }
                            }
                        }
                   }
                   $em->flush();
           } else {
               $erreur[]='Aucune chambre n\a ete selectionnee';
           }
           
           //ajoute le coupon et verifie si son utilisation est correcte ou non
           $couponCodePromo=$request->get('coupon');
           if ($couponCodePromo){
               $coupon=$em->getRepository('ResanetMainBundle:Coupon')->findOneBy(array('codePromo'=>$couponCodePromo));
               if (!$coupon){
                   $erreur[]='Le coupon donne ne correspond a aucun coupon';
                    } else {
                        if (!$coupon->estValide($resa->getDateCreation())){
                        $erreur[]='Le coupon '.$coupon->getCodePromo().'n\'est pas valide ou le nombre maximum a ete utilise'; 
                        } else {
                            $resa->setCoupon($coupon);
                        }
                    } 
           }
           //s'il y a une reduction personnel la rajoute a l'objet
           $reductionPers=$request->get('reductionPers');
           if($reductionPers){
              $resa->setReduction($reductionPers);
           }

           //s'il y a un commentaire
           $commentaire=$request->get('commentaires');
           if($commentaire){
              $resa->setCommentaires($commentaire);
           }

           //verifie l'etat de la reservation en fonction des donnees indiquees notamment le checkin
           $resa->checkEtat($arrhes);
           //teste la validite de l'objet Reservation
           $erreurResa=$validator->validate($resa);
           if (count($erreurResa) > 0) {
               foreach ($erreurResa as $err){
                $erreur[]=$err->getMessage();
               }
            }
           $em->flush();

           //s'il y a un paiement
           $paiementTypes=$request->get('paiementType');
           $paiementMontants=$request->get('paiementMontant');
           $paiementDates=$request->get('paiementDate');

           //s'il y a un paiement on procede a la creation des objets
            if($paiementTypes){
                
               foreach($paiementTypes as $key=>$value){
                   $paiement=new Paiement();
                    //date Paiement
                   $datePaiement=date_create_from_format('d#m#Y', $paiementDates[$key]);            
                   if(!$datePaiement)$erreur[]='La date de paiement n\'est pas au format jj/mm/aaaa';
                   else  $paiement->setDatePaiement($datePaiement);
                   
                    $paiement->setMontant($paiementMontants[$key]);
                    $paiement->setMoyen($paiementTypes[$key]);
                    //test de la validite de l'objet paiement
                    $erreurPaie=$validator->validate($paiement);
                    if (count($erreurPaie) > 0) {
                        foreach ($erreurPaie as $err){
                        $erreur[]=$err->getMessage();
                        } 
                    } else {
                        // l'ajoute a la reservation
                        $paiement->setReservation($resa);
                        $resa->addPaiement($paiement);
                        $em->persist($paiement);
                    }
                }
                if ($resa->getTotalPaye()<($arrhes)*($resa->getTotalChambres()-$resa->getTotalReductions())/100){
                    $erreur[]='Le paiement minimum est de '.$arrhes.'% du total des chambres ';
                }
                $resa->checkEtat($arrhes);
                $em->flush();
           }

            //s'il y a des options
           $optionResIds=$request->get('option');
           $optionResQuantite=$request->get('optionQuantite');

           //s'il y a une option on procede a la creation des objets
            if($optionResIds){
               
               foreach($optionResIds as $key=>$value){
                   $reservationOptionRes=new ReservationOptionRes();   
                    //recherche l'option correspondante
                   $optionRes=$em->getRepository('ResanetMainBundle:OptionRes')->find($optionResIds[$key]);

                   if (is_null($optionRes)==false){
                      $reservationOptionRes->setOptionRes($optionRes);  
                    } else {
                        $erreur[]='Aucune option ne correspond a celle transmise';
                    }
                    //recherche de l'unicite d'une option dans une reservation
                    $bool=true;
                    foreach ($resa->getReservationOptionRes() as $resOptRes){
                          if ($resOptRes->getOptionRes()==$optionRes){
                              $bool=false;
                              $erreur[]='Vous tentez de rajouter 2 fois la même option';
                          }
                     }
                     $reservationOptionRes->setQuantite($optionResQuantite[$key]);               


                    //test de la validite de l'objet paiement
                    $erreurOption=$validator->validate($reservationOptionRes);
                    if (count($erreurOption) > 0) {
                        foreach ($erreurOption as $err){
                        $erreur[]=$err->getMessage();
                        } 
                    } else {
                        // l'ajoute a la reservation
                        $reservationOptionRes->setReservation($resa);
                        $em->persist($reservationOptionRes);
                        $em->flush();
                    }
                }
           }

            //Test presence Occupant et Personne
           $occupantNomTab=$request->get('occupantNom');
           $occupantPrenomTab=$request->get('occupantPrenom');
           $occupantDateTab=$request->get('occupantDate');
           $occupantChambreTab=$request->get('occupantChambre');

           //s'il y a une personne ou plusieurs personnes on va instancier les objets Personne et Occupant
            if($occupantNomTab){
               foreach($occupantNomTab as $key=>$value){
                   //faire la recherche dans la base de donnees
                   $personnes=$em->getRepository('ResanetMainBundle:Personne')->findBy(array('nom'=>$occupantNomTab[$key], 'prenom'=>$occupantPrenomTab[$key]));
                   if (!$personnes){
                       $personne=new Personne();
                       //ajoute l'attribut nom et personne
                        $personne->setNom($occupantNomTab[$key]);
                        $personne->setPrenom($occupantPrenomTab[$key]);  
                       } 
                       else 
                      $personne=$personnes[0];
                    //ajoute la date de naissance
                    $dateNaissance=date_create_from_format('d#m#Y', $occupantDateTab[$key]);            
                     if(!$dateNaissance)$erreur[]='La date de naissance n\'est pas au format jj/mm/aaaa';
                      else  $personne->setDateNaissance($dateNaissance);
                                      
                    //test de la validite de l'objet personne
                    $erreurPersonne=$validator->validate($personne);
                    if (count($erreurPersonne) > 0) {
                        foreach ($erreurPersonne as $err){
                        $erreur[]=$err->getMessage();
                        } 
                    } else {
                        $em->persist($personne);
                        $em->flush();
                        //verifie si la chambre correspond bien a la reservation
                        $chambre=$em->getRepository('ResanetMainBundle:Chambre')->find($occupantChambreTab[$key]);
                        if ($resa->getChambres()->contains($chambre)){
                            $occupant=new Occupant();
                            $occupant->setChambre($chambre);
                            $occupant->setReservation($resa);
                            $occupant->setPersonne($personne);
                            $resa->addOccupant($occupant);
                            $em->persist($occupant);
                            $em->flush();
                        } else { 
                            $erreur[]='Vous tentez d\'attribuer une chambre a une personne ne faisant pas partie de la reservation';
                          }
                    }
                }
           }

            if (count($erreur)>0){
               $em->getConnection()->rollback();
               $em->close();
                return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
            } else {
                $em->getConnection()->commit();
                
               return new Response(json_encode(array('rep'=>1, 'etat'=>$resa->getEtat(), 'id'=>$resa->getId())));
            }
       } catch (\Exception $e) {
               $em->getConnection()->rollback();
               $em->close();
               $erreur[]='Veuillez nous signaler l\'erreur svp';
               return new Response(json_encode(array('rep'=>0, 'erreur'=>$erreur)));
           }
}




    /**
     * supprime une reservation, si la reservation est sans paiement 
      * @return view:reservationAnnulationFormulaire.html.twig
     */
    public function annulerReservationAction(){
       $validator=$this->get('validator');
       $request=$this->getRequest();
       $em=$this->getDoctrine()->getEntityManager();
       $erreur=array();
       $arrhes=$this->container->getParameter('arrhes');
       $id=$request->get('idResaForm');
       $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
       if (!$resa) {
           $erreur[]='Aucune reservation ne correspond a vôtre requête';
           return $this->updateReservationAction($id, $erreur);
       }
       
       if ($resa->getTotalPaye()==0){
           $em->remove($resa);
           $em->flush();
           return $this->listeReservationAction();
       } else {
            //creation de l'objet Annulation
            //date Option
          $annulation=new Annulation();
          $dateAnnulation=date_create_from_format('d#m#Y', $request->get('dateAnnulation'));            
          if(!$dateAnnulation) $erreur[]='La date d\'annulation n\'est pas au format jj/mm/aaaa';
           else {
              $dateAnnulation->setTime(01,00);
              $annulation->setDate($dateAnnulation);
           }
           //ajout raison
           $annulation->setRaison($request->get('raison'));
           $annulation->setClient($resa->getClient());
           $annulation->setReservation($resa);
           //on enregistre l'annulation
           $resa->setAnnulation($annulation);
           
              //
               //supprimer les options de la reservation
            foreach ($resa->getReservationOptionRes() as $resOptRes){
                       $resa->getReservationOptionRes()->removeElement($resOptRes);
                       $em->remove($resOptRes);
           }
            foreach ($resa->getOccupants() as $occupant){
                                   $resa->getOccupants()->removeElement($occupant);
                                   $em->remove($occupant);
                                 }
           //on calcule le montant de la reservation en fonction des arrhes
           
           $total=$resa->getTotalChambres()-$resa->getTotalReductions();
           $montant=round($arrhes*$total*$resa->getRetenueArrhes()/10000, 2);
           $annulation->setMontant($montant);         
           //test de la validite de l'objet annulation
           $erreurAnnulation=$validator->validate($annulation);
           if (count($erreurAnnulation) > 0) {
                foreach ($erreurAnnulation as $err){
                      $erreur[]=$err->getMessage();
                 }
           }
           
           //si probleme renvoie le formulaire avec apparition des erreurs
           if (count($erreur)>0){
               return $this->updateReservationAction($id, $erreur);
           } else {
               $resa->setEtat('Annulée');
               if ($resa->getTotalPaye()==$montant){$resa->setEtat('Soldée');}
               $em->persist($annulation);
               $em->flush();
               return $this->editerReservationAnnuleeAction($resa->getId());
           }
        }
       
}
    

    /**
     * renvoie un formulaire de creation de reservation
     * @return view:reservationFormulaire.html.twig
     */
    public function ajouterReservationAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $dateOption= new \DateTime(date('Y-m-d'));
       $dateOption->add(new \DateInterval('P7D'));
         return $this->render('ResanetMainBundle:Formulaire:reservationFormulaire.html.twig', array('dateOption'=>$dateOption));
    }
    
    /**
     * renvoie un formulaire d'edition de formulaire de reservation
     * @param int
     * @param string
     * @return view:reservationFormulaire.html.twig
     */
    public function updateReservationPlanningAction(){
        $em=$this->getDoctrine()->getEntityManager();
        $resa=new Reservation();
        $request=$this->getRequest();
        $chambres=$request->get('chambre');
        $resa->setDateArrivee(\DateTime::createFromFormat('d-m-Y',$request->get('dateArr')));
        $dateDep= \DateTime::createFromFormat('d-m-Y',$request->get('dateDep'));
        $dateDep->add(new \DateInterval('P1D')); 
        $resa->setDateDepart($dateDep);
        $dateOption= new \DateTime(date('Y-m-d'));
        $resa->setDateCreation($dateOption);
        $dateOption->add(new \DateInterval('P7D'));
        foreach ($chambres as $chbre){
            $chbreRes=$em->getRepository('ResanetMainBundle:Chambre')->findOneByNom($chbre);
            
            if ($chbreRes->pasDeReservationPeriode($resa->getDateArrivee()->format('Y-m-d'), $resa->getDateDepart()->format('Y-m-d'))) {
                $resa->addChambre($chbreRes);
                }
        }
         return $this->forward('ResanetMainBundle:Reservation:envoyerFormulairePrerempli', array('resa'=>$resa, 'erreur'=>null));
    }
    
    
    
    
    /**
     * renvoie un formulaire d'edition de formulaire de reservation
     * @param int
     * @param string
     * @return view:reservationFormulaire.html.twig
     */
    public function updateReservationAction($id, $erreur=null){
        $em=$this->getDoctrine()->getEntityManager();
        $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
        if(!$resa){
            return $this->listeReservationAction();
        }
        if($resa->getEtat()=='Soldée'){
            return $this->render('ResanetMainBundle:Formulaire:reservationVoir.html.twig', array('resa'=>$resa));
        }
        if($resa->getEtat()=='Annulée'){
            return $this->editerReservationAnnuleeAction($resa->getId());
        }
        return $this->forward('ResanetMainBundle:Reservation:envoyerFormulairePrerempli', array('resa'=>$resa, 'erreur'=>$erreur));
    }  
        
    public function envoyerFormulairePrerempliAction($resa, $erreur){
        $em=$this->getDoctrine()->getEntityManager();
         //recuperer toutes les options et les envoient
        $optionRes=$em->getRepository('ResanetMainBundle:OptionRes')->findAll();
        //recuperer toutes les chambres de la reservation et les compilent dans un tableau
        $tabChambresRes=array();
        foreach ($resa->getChambres() as $chambre){
            $tabChambresRes[]=array('prix'=>$chambre->getCategorie()->calculPrixPer($resa->getDateArrivee()->format('Y-m-d'),$resa->getDateDepart()->format('Y-m-d'), $resa->getDateCreation()),
                                  'categorie'=>$chambre->getCategorie()->getNom(), 
                                  'chambre'=>$chambre->getNom(), 
                                  'id'=>$chambre->getId());
        }
        
        //recuperer toutes les chambres libres pour la periode
        $tabChambresLibres=$this->listeChambresDispoAction($resa->getDateArrivee()->format('Y-m-d'),$resa->getDateDepart()->format('Y-m-d'), $resa->getDateCreation());
        $parametres=array('resa'=>$resa, 'chambres'=>$tabChambresRes, 'chambresLibres'=>$tabChambresLibres, 'options'=>$optionRes);
        if ($erreur) $parametres['erreur']=$erreur;
        return $this->render('ResanetMainBundle:Formulaire:reservationFormulaire.html.twig', $parametres);
    }
    
     /**
     * renvoie un formulaire d'edition de formulaire de reservation
     * @return view:reservationFormulaire.html.twig
     */
    public function editerReservationAnnuleeAction($id){
        $em=$this->getDoctrine()->getEntityManager();
        $resa=$em->getRepository('ResanetMainBundle:Reservation')->find($id);
        foreach ($resa->getChambres() as $chambre){
            $tabChambresRes[]=array('prix'=>$chambre->getCategorie()->calculPrixPer($resa->getDateArrivee()->format('Y-m-d'),$resa->getDateDepart()->format('Y-m-d'), $resa->getDateCreation()),
                                  'categorie'=>$chambre->getCategorie()->getNom(), 
                                  'chambre'=>$chambre->getNom(), 
                                  'id'=>$chambre->getId());
        }
        return $this->render('ResanetMainBundle:Formulaire:reservationAnnuleeFormulaire.html.twig', array('resa'=>$resa, 'chambres'=>$tabChambresRes));
    }
    
    
    /**
     * retourne un tableau de tableau de chambres disponibles, avec la categorie, le prix, 
     * pour une periode donnee sous la forme de 2 strings au format yyyy-mm-dd $date1, $date2.
     * Dans le cas d'une creation de reservation $date3=null. Si edition reservation date3=dateCreation
     * @param string
     * @param string
     * @param \DateTime
     * @return array()
     */
    public function listeChambresDispoAction($date1, $date2, $date3){ 
        $em=$this->getDoctrine()->getEntityManager();
        $categories=$em->getRepository('ResanetMainBundle:Categorie')->findAll();
        $tabChambres=array();
        foreach ($categories as $cat){
            if ($cat->isPrixDefPer($date1, $date2)){
                $prix=$cat->calculPrixPer($date1, $date2, $date3);
                foreach($cat->getChambres() as $chbre){
                    if ($chbre->estDispoPeriode($date1,$date2)){
                        $tabChambres[]=array('prix'=>$prix,'categorie'=>$cat->getNom(), 'chambre'=>$chbre->getNom(), 'id'=>$chbre->getId());
                    }
                }
            }
        }
        return $tabChambres;
    }
    
    
    /**
     * renvoie une liste d'options pour une creation
     * @return array()
     */
    public function listeOptionsJsonAction(){ 
        $em=$this->getDoctrine()->getEntityManager();
        $options=$em->getRepository('ResanetMainBundle:OptionRes')->findAll();
        $tabOptions=array();
        foreach ($options as $option){
            $tabOptions[]=array('prix'=>$option->getPrix(),'nom'=>$option->getNom(), 'id'=>$option->getId());
            }
        return new Response(json_encode($tabOptions));
    }
    
    /**
     * met a jour les reservations, supprime les reservations dont la date d'option est depassee et sans paiement
     * @todo a rendre automatique - Cron Tab
     * @return Controller
     */
    public function etatReservationAction(){ 
        $em=$this->getDoctrine()->getEntityManager();
        $reservations=$em->getRepository('ResanetMainBundle:Reservation')->findAll();
        $arrhes=$this->container->getParameter('arrhes');
        //verifie l'etat de la reservation
        foreach ($reservations as $resa){
            if ($resa->getEtat()!='Soldée'){
                $resa->checkEtat($arrhes);
                if ($resa->getEtat()=='Annulée'){
                // pour toutes les reservations annulees
                //s'il n'y a pas de paiements les supprimer
                    if ($resa->getTotalPaye()==0){
                             $em->remove($resa);
                    } else {
                        if (!$resa->getAnnulation()){
                              //s'il n'y a des paiements et pas d'annulation, creer une annulation et l'attribuer a la resa
                            //creation de l'objet Annulation
                           $annulation=new Annulation();
                           $dateAnnulation=new \DateTime(date('Y-m-d')); 
                           $annulation->setDate($dateAnnulation);
                           $annulation->setRaison('Automatique');
                           $annulation->setClient($resa->getClient());
                           $annulation->setReservation($resa);
                           //on enregistre l'annulation
                           $resa->setAnnulation($annulation);
                           //supprimer les options de la reservation
                            foreach ($resa->getReservationOptionRes() as $resOptRes){
                                       $resa->getReservationOptionRes()->removeElement($resOptRes);
                                       $em->remove($resOptRes);
                                     }
                            foreach ($resa->getOccupants() as $occupant){
                                       $resa->getOccupants()->removeElement($occupant);
                                       $em->remove($occupant);
                                     }
                           //on calcule le montant de la reservation en fonction des arrhes

                           $total=$resa->getTotalChambres()-$resa->getTotalReductions();
                           $montant=round($arrhes*$total*$resa->getRetenueArrhes()/10000, 2);
                           $annulation->setMontant($montant);         
                           $resa->setEtat('Annulée');
                           if ($resa->getTotalPaye()==$montant){
                               $resa->setEtat('Soldée');
                            }
                           $em->persist($annulation);
                        }
                    }

                }
            }
        }
        $em->flush();
        return $this->listeReservationAction();
    }
    
    
}
