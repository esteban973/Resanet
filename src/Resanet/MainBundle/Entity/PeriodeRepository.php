<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PeriodeRepository
 */
class PeriodeRepository extends EntityRepository
{
    public function tableauPeriode($id){
        $em = $this->getEntityManager();
        $qb= $em->createQueryBuilder();
        $qb->select('p')
           ->from('ResanetMainBundle:Periode', 'p');
        if(isset($id)){
           $qb->where('p.id <> :id')
           ->setParameter('id', $id);
        }
        $results=$qb->getQuery()->getResult();
        $tab=array();
        foreach($results as $result){
            $tab[]=array('dateDebut'=>$result->getDateDebut(),'dateFin'=>$result->getDateFin(),);
        }
        return $tab;
    }
    
       
}