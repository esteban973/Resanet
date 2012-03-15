<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChambreRepository extends EntityRepository
{
    public function chercheChbreCategorie($catId){
        $em=  $this->getEntityManager();
        $qb=$em->createQueryBuilder();
        $qb->select('c')
           ->from('ResanetMainBundle:Chambre', 'c')
           ->from('ResanetMainBundle:Categorie', 'cat')
           ->where('c.categorie=cat' )
           ->andWhere('cat.id=:id')
            ->setParameter('id', $catId)  ;
         return $qb->getQuery()->getResult();
    }
    
    
}