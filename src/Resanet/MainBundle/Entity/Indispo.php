<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Indispo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Resanet\MainBundle\Entity\IndispoRepository")
 */
class Indispo
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
     * @var date $date
     *
     * @ORM\Column(name="dateDebut", type="date")
     * @Assert\Date()
     */
    private $dateDebut;

    /**
     * @var date $dateFin
     *
     * @ORM\Column(name="dateFin", type="date")
     * @Assert\Date()
     */
    private $dateFin;

     /**
     * 
     * @ORM\ManyToOne(targetEntity="Chambre", inversedBy="indispos")
     * @ORM\JoinColumn(name="chambre_id", referencedColumnName="id", nullable=false)
    */
    private $chambre;
  

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

    /**
     * Set indispo
     *
     * @param Resanet\MainBundle\Entity\Chambre $indispo
     */
    public function setIndispo(\Resanet\MainBundle\Entity\Chambre $indispo)
    {
        $this->indispo = $indispo;
    }

    /**
     * Get indispo
     *
     * @return Resanet\MainBundle\Entity\Chambre 
     */
    public function getIndispo()
    {
        return $this->indispo;
    }

    /**
     * Set chambre
     *
     * @param Resanet\MainBundle\Entity\Chambre $chambre
     */
    public function setChambre(\Resanet\MainBundle\Entity\Chambre $chambre)
    {
        $this->chambre = $chambre;
    }

    /**
     * Get chambre
     *
     * @return Resanet\MainBundle\Entity\Chambre 
     */
    public function getChambre()
    {
        return $this->chambre;
    }
    
    /**
     * 
     * @Assert\True(message="La date de fin est supérieure à celle de début Serveur")
     */
    public function isDatesValides(){
     if ($this->dateFin>=$this->dateDebut) return true; 
      else return false; 
    }
    
      public function enChaine(){
       return 'Chambre '.$this->chambre->getNom().' indisponible du '.$this->dateDebut->format('d-m-Y').' au '.$this->dateFin->format('d-m-Y');
    }
    
    public function toArray(){
        $coupon=array();
        $coupon['id']=$this->id;
        $coupon['chambre']=$this->chambre->getNom();
        $coupon['dateDebut']=$this->dateDebut->format('d-m-Y');
        $coupon['dateFin']=$this->dateFin->format('d-m-Y');
        $coupon['form']='<input type="radio" id="'.$this->id.'" name="radio" />';
        return $coupon;
    }
    
    
}