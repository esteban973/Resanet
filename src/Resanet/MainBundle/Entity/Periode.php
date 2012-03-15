<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Periode
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Resanet\MainBundle\Entity\PeriodeRepository")
 */

class Periode
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date $dateDebut
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var date $dateFin
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;

     /**
     * @ORM\OneToMany(targetEntity="PeriodeCategorie", mappedBy="periode", cascade={"remove"})
     */
    private $periodeCategories;
    
    /**
     * @var date $dateFin
     *
     * @Assert\True(message="La date de début est supérieure à la date de fin")
     */
    public function isDatesValides(){
        return ($this->dateFin>=$this->dateDebut)? true:false;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateDebut
     *
     * @param date $dateDebut
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * Get dateDebut
     *
     * @return date 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param date $dateFin
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    }

    /**
     * Get dateFin
     *
     * @return date 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }
    public function __construct()
    {
        $this->periodeCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add periodeCategories
     *
     * @param Resanet\MainBundle\Entity\PeriodeCategorie $periodeCategories
     */
    public function addPeriodeCategorie(\Resanet\MainBundle\Entity\PeriodeCategorie $periodeCategories)
    {
        $this->periodeCategories[] = $periodeCategories;
    }

    /**
     * Get periodeCategories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPeriodeCategories()
    {
        return $this->periodeCategories;
    }
    
    
    public function getIdCat(){
        $ids=array();
        foreach ($this->periodeCategories as $value){
            $ids[]=$value->getCategorie()->getId();
        }
        return $ids;
    }
    
    public function getIdCatPrix()
    {
        $ids=array();
        foreach ($this->periodeCategories as $value){
            $ids[$value->getCategorie()->getId()]=$value->getPrix();
        }
        return $ids;
    }
    
    public function toArray($categories){
        $reduction=array();
        $reduction['id']=$this->id;
        $reduction['dateDebut']=$this->dateDebut->format('d-m-Y');
        $reduction['dateFin']=$this->dateFin->format('d-m-Y');
        $idPrix=$this->getIdCatPrix();
        foreach($categories as $cat){
            $reduction[$cat->getId()]=(isset($idPrix[$cat->getId()]))? $idPrix[$cat->getId()]:'-';
        }
        
        return $reduction;
    }
    
    public function enChaine(){
        return 'Période du '.$this->dateDebut->format('d-m-Y').' au '.$this->dateFin->format('d-m-Y');
    }
    
}