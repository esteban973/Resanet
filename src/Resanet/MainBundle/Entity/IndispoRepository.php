<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PeriodeRepository
 */
class IndispoRepository extends EntityRepository
{
    public function tableauIndispo($id, $chambreId){
        $em = $this->getEntityManager();
        $qb= $em->createQueryBuilder();
        $qb->select('i')
           ->from('ResanetMainBundle:Indispo', 'i')
           ->from('ResanetMainBundle:Chambre', 'c')
           ->where('i.chambre = c')
           ->andWhere('c.id = :chambreId')
           ->setParameter('chambreId', $chambreId);
        if(isset($id)){
           $qb->andWhere('i.id <> :id')
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