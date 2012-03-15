<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Reduction
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Reduction
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
     * @var integer $pourcent
     * @Assert\Max(limit = 100, message = "Le % de reduction doit être inférieur à 100")
     * @Assert\Min(limit = 0, message = "Le % de reduction doit être supérieur à 0")
     * @ORM\Column(name="pourcent", type="integer")
     */
    private $pourcent;

    /**
     * @var date $dateDebEff
     * @Assert\Date()
     * @ORM\Column(name="dateDebEff", type="date")
     */
    private $dateDebEff;

    /**
     * @var date $dateFinEff
     * @Assert\Date()
     * @ORM\Column(name="dateFinEff", type="date")
     */
    private $dateFinEff;

    /**
     * @var date $dateDebPeriod
     * @Assert\Date()
     * @ORM\Column(name="dateDebPeriod", type="date")
     */
    private $dateDebPeriod;

    /**
     * @var date $dateFinPeriod
     * @Assert\Date()
     * @ORM\Column(name="dateFinPeriod", type="date")
     */
    private $dateFinPeriod;

    /**
     * @var string $libelle
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     * @Assert\NotBlank(message="Le nom doit être renseigné")
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity="Categorie", inversedBy="reductions")
     */
    private $categories;

    /**
     *
     * @Assert\True(message="La date de début de validité est supérieure à celle de fin")
     */
    public function isValidDateDebFinEff(){
        return ($this->dateFinEff>$this->dateDebEff) ? true : false;
    }
    
    /**
     *
     * @Assert\True(message="La date de début de validité est supérieure à celle de fin")
     */
    public function isValidDateDebFinPeriod(){
        return ($this->dateFinPeriod>$this->dateDebPeriod) ? true : false;
    }
    
    
     /**
     *
     * @Assert\True(message="La date de fin de validité est supérieure à celle de fin de période")
     */
    public function isValidDateFinEffFinPeriod(){
        return ($this->dateFinPeriod>$this->dateFinEff) ? true : false;
    }
    
     /**
     *
     * @Assert\True(message="La date de fin de validité est supérieure à celle de fin de période")
     */
    public function isACheval(){
        return  true;
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
     * Set pourcent
     *
     * @param integer $pourcent
     */
    public function setPourcent($pourcent)
    {
        $this->pourcent = $pourcent;
    }

    /**
     * Get pourcent
     *
     * @return integer 
     */
    public function getPourcent()
    {
        return $this->pourcent;
    }

    /**
     * Set dateDebEff
     *
     * @param date $dateDebEff
     */
    public function setDateDebEff($dateDebEff)
    {
        $this->dateDebEff = $dateDebEff;
    }

    /**
     * Get dateDebEff
     *
     * @return date 
     */
    public function getDateDebEff()
    {
        return $this->dateDebEff;
    }

    /**
     * Set dateFinEff
     *
     * @param date $dateFinEff
     */
    public function setDateFinEff($dateFinEff)
    {
        $this->dateFinEff = $dateFinEff;
    }

    /**
     * Get dateFinEff
     *
     * @return date 
     */
    public function getDateFinEff()
    {
        return $this->dateFinEff;
    }

    /**
     * Set dateDebPeriod
     *
     * @param date $dateDebPeriod
     */
    public function setDateDebPeriod($dateDebPeriod)
    {
        $this->dateDebPeriod = $dateDebPeriod;
    }

    /**
     * Get dateDebPeriod
     *
     * @return date 
     */
    public function getDateDebPeriod()
    {
        return $this->dateDebPeriod;
    }

    /**
     * Set dateFinPeriod
     *
     * @param date $dateFinPeriod
     */
    public function setDateFinPeriod($dateFinPeriod)
    {
        $this->dateFinPeriod = $dateFinPeriod;
    }

    /**
     * Get dateFinPeriod
     *
     * @return date 
     */
    public function getDateFinPeriod()
    {
        return $this->dateFinPeriod;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }
    
    public function toArray(){
        $reduction=array();
        $reduction['id']=$this->id;
        $reduction['nom']=$this->libelle;
        $reduction['pourcentage']=$this->pourcent.' %';
        $reduction['dateDebEff']=$this->dateDebEff->format('d-m-Y');
        $reduction['dateFinEff']=$this->dateFinEff->format('d-m-Y');
        $reduction['dateDebPeriod']=$this->dateDebPeriod->format('d-m-Y');
        $reduction['dateFinPeriod']=$this->dateFinPeriod->format('d-m-Y');
        $reduction['form']='<input type="radio" id="'.$this->id.'" name="radio" />';
        return $reduction;
    }
    
    public function enChaine(){
        return 'Reduction :'.$this->libelle.' Remise :'.$this->pourcent.'%  valide du '.$this->dateDebEff->format('d-m-Y').' au '.$this->dateFinEff->format('d-m-Y').' pour la période du '.$this->dateDebPeriod->format('d-m-Y').' au '.$this->dateFinPeriod->format('d-m-Y');
    }
    
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add categories
     *
     * @param Resanet\MainBundle\Entity\Categorie $categories
     */
    public function addCategorie(\Resanet\MainBundle\Entity\Categorie $categories)
    {
        $this->categories[] = $categories;
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}