<?php

namespace Resanet\MainBundle\Fonctions;

class CalculDates{
    
    /**
     * reçoit un tableau $tab de periode dateTime=dateDebut et dateTime=dateFin
     * @return boolean 
     */
    static public function aCheval($dateDebut,$dateFin, $tab){
       
        foreach($tab as $periode){
               if (($periode['dateDebut']<=$dateDebut)&&($periode['dateFin']>=$dateDebut)){
                    return false;
                }
                if (($periode['dateDebut']>=$dateDebut)&&($periode['dateDebut']<=$dateFin)){
                    return false;
                }
       }
       return true;
    }
    
    /**
     * à une date envoyée en dd/mm/yyyy renvoie une date yyyy-mm-dd
     * @param string
     * @return string 
     */
    static public function transformationDate($date){
       return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2);
    }
    
    /**
     * à une date envoyée en dd/mm/yyyy hh:mm renvoie une date yyyy-mm-dd hh:mm
     * @param string
     * @return string 
     */
    static public function transformationDateHeure($date){
       return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2).' '.substr($date,10,5).':00';
    }

}

