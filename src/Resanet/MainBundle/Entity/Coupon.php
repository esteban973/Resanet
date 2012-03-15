<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Coupon
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Coupon
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
     * @var string $codePromo
     *
     * @ORM\Column(name="codePromo", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le code promo doit être renseigné")
     */
    private $codePromo;

    /**
     * @var integer $pourcentage
     *
     * @ORM\Column(name="pourcentage", type="integer", nullable=true)
     * @Assert\Max(limit=100, message="Le pourcentage maximum est 100")
     * @Assert\Min(limit=0, message="Le pourcentage maximum est 0")
     */
    private $pourcentage;

    /**
     * @var integer $montant
     *
     * @ORM\Column(name="montant", type="integer", nullable=true)
     * @Assert\Max(limit=300, message="Le montant maximum est 300")
     * @Assert\Min(limit=0, message="Le montant minimum est 0")
     */
    private $montant;

    /**
     * @var date $dateDebut
     *
     * @ORM\Column(name="dateDebut", type="date")
     * @Assert\Date(message="La date n'est pas valide")
     */
    private $dateDebut;

    /**
     * @var date $dateFin
     *
     * @ORM\Column(name="dateFin", type="date")
     * @Assert\Date(message="La date n'est pas valide")
     */
    private $dateFin;

    /**
     * @var integer $nbDebut
     *
     * @ORM\Column(name="nbDebut", type="integer")
      * @Assert\Max(limit=300, message="Le montant maximum est 300")
     * @Assert\Min(limit=0, message="Le montant minimum est 0")
     */
    private $nbDebut;

    /**
     * @var integer $nbUtilise
     *
     * @ORM\Column(name="nbUtilise", type="integer")
     * @Assert\Max(limit=300, message="Le montant maximum est 300")
     * @Assert\Min(limit=0, message="Le montant minimum est 0")
     */
    private $nbUtilise=0;
    
    
     /**
     * 
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="coupon")
     *
    */
    private $reservations;
    
    
    /**
     *
     * @return type 
     * @Assert\True(message="La date de fin doit être supérieure à la date de début")
     * 
     */
    public function isDatesValides(){
      if ($this->dateFin>=$this->dateDebut) return true; 
      else return false;  
    }
    
    /**
     *
     * @return type 
     * @Assert\True(message="Vous devez remplir soit le montant soit le pourcentage")
     * 
     */
    public function isMontantouPourcent(){
      if (((is_null($this->montant))&&($this->pourcentage>0))||((is_null($this->pourcentage))&&($this->montant>0))) return true; 
      else return false;  
    }
    
    
    /**
     *
     * @return type 
     * @Assert\True(message="Le nombre de coupons de départ est plus petit que le nombre de coupons déjà utilisé")
     * 
     */
    public function isNbDepartPlusGrand(){
      if ($this->nbDebut>=$this->nbUtilise) return true; 
      else return false;  
    }
    
    
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set codePromo
     *
     * @param string $codePromo
     */
    public function setCodePromo($codePromo)
    {
        $this->codePromo = $codePromo;
    }

    /**
     * Get codePromo
     *
     * @return string 
     */
    public function getCodePromo()
    {
        return $this->codePromo;
    }

    /**
     * Set pourcentage
     *
     * @param integer $pourcentage
     */
    public function setPourcentage($pourcentage)
    {
        $this->pourcentage = $pourcentage;
    }

    /**
     * Get pourcentage
     *
     * @return integer 
     */
    public function getPourcentage()
    {
        return $this->pourcentage;
    }

    /**
     * Set montant
     *
     * @param integer $montant
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
    }

    /**
     * Get montant
     *
     * @return integer 
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set dateDebut
     *
     * @param date $dateDebut
     */
    public function setDateDebut($dateDebut)
    {
$this->dateDebut=$dateDebut;        
//$this->dateDebut = new \DateTime($dateDebut);
    }

    /**
     * Get dateDebut
     *
     * @return date 
     */
    public function getDateDebut()
    {
       return $this->dateDebut;
// return ($this->dateDebut->format("Y-m-d"));
    }

    /**
     * Set dateFin
     *
     * @param date $dateFin
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
//$this->dateFin = new \DateTime($dateFin);
    }

    /**
     * Get dateFin
     *
     * @return date 
     */
    public function getDateFin()
    {
        return $this->dateFin;
// return ($this->dateFin->format("Y-m-d"));
    }

    /**
     * Set nbDebut
     *
     * @param integer $nbDebut
     */
    public function setNbDebut($nbDebut)
    {
        $this->nbDebut = $nbDebut;
    }

    /**
     * Get nbDebut
     *
     * @return integer 
     */
    public function getNbDebut()
    {
        return $this->nbDebut;
    }

    /**
     * Set nbUtilise
     *
     * @param integer $nbUtilise
     */
    public function setNbUtilise($nbUtilise)
    {
        $this->nbUtilise = $nbUtilise;
    }

    /**
     * Get nbUtilise
     *
     * @return integer 
     */
    public function getNbUtilise()
    {
        return $this->nbUtilise;
    }

    /**
     * Add reservations
     *
     * @param Resanet\MainBundle\Entity\Reservation $reservations
     */
    public function addReservation(\Resanet\MainBundle\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;
    }

    /**
     * Get reservations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }
    
    public function enChaine(){
        $remise=!($this->montant) ? $this->pourcentage.' %' : $this->montant.' €';
        return 'Code Promo :'.$this->codePromo.' Remise :'.$remise.' valide du '.$this->dateDebut->format('d-m-Y').' au '.$this->dateFin->format('d-m-Y');
    }
    
    public function toArray(){
        $coupon=array();
        $coupon['id']=$this->id;
        $coupon['codepromo']=$this->codePromo;
        $coupon['montant']=!($this->montant) ? '-' : $this->montant.' €';
        $coupon['pourcentage']=!($this->pourcentage) ? '-' : $this->pourcentage.' %';
        $coupon['nbDebut']=$this->nbDebut;
        $coupon['nbUtilise']=$this->getReservations()->count();
        $coupon['dateDebut']=$this->dateDebut->format('d-m-Y');
        $coupon['dateFin']=$this->dateFin->format('d-m-Y');
        
        return $coupon;
    }
    
    
    /**
     * vérifie si on peut utiliser toujours le coupon en fonction des dates et du nombre max d'utilisation
     * @param DateTime
     * @return boolean
     */
    public function estValide($date){
        return (($date>=$this->getDateDebut())&&($date<=$this->getDateFin())&&($this->getReservations()->count()<$this->getNbDebut()));
    }
    
}