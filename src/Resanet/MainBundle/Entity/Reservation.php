<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Reservation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Resanet\MainBundle\Entity\ReservationRepository")
 */
class Reservation
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
     * @var datetime $dateCreation
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     * @Assert\NotNull(message="La date de création ne doit être pas vide")
     */
    private $dateCreation;

    /**
     * @var string $etat
     *
     * @ORM\Column(name="etat", type="string", length=255)
     * @Assert\NotNull()
     * 
     */
    private $etat;

    /**
     * @var date $dateArrivee
     *
     * @ORM\Column(name="dateArrivee", type="date")
     * @Assert\NotNull(message="La date d'arrivée doit être renseignée")
     * @Assert\Date()
     */
    private $dateArrivee;

    /**
     * @var date $dateDepart
     *
     * @ORM\Column(name="dateDepart", type="date")
     * @Assert\NotNull()
     * @Assert\Date()
     */
    private $dateDepart;

    /**
     * @var time $heureArrivee
     *
     * @ORM\Column(name="heureArrivee", type="string", nullable=true)
     * @Assert\Time()
     */
    private $heureArrivee;

    /**
     * @var datetime $dateCheckIn
     *
     * @ORM\Column(name="dateCheckIn", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateCheckIn;

    /**
     * @var datetime $dateCheckOut
     *
     * @ORM\Column(name="dateCheckOut", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateCheckOut;

    /**
     * @var datetime $dateOption
     *
     * @ORM\Column(name="dateOption", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateOption;
    
    /**
     * @var datetime $dateOption
     *
     * @ORM\Column(name="reduction", type="integer", nullable=true)
     *
     */
    private $reduction;
    
    /**
     * @var string $commentaires
     *
     * @ORM\Column(name="commentaires", type="string", length=255, nullable=true)
     */
    private $commentaires;
    
    
    /**
     * 
     * @ORM\OneToOne(targetEntity="Annulation", mappedBy="reservation")
    */
    private $annulation;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="reservations")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false)
    */
    private $client;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Paiement", mappedBy="reservation")
     * 
     */
    private $paiements;
    
     /**
     * 
     * @ORM\OneToMany(targetEntity="ReservationOptionRes", mappedBy="reservation", cascade={"persist, remove"})
     *
    */
    private $reservationOptionRes;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Coupon", inversedBy="reservations")
     * 
     */
    private $coupon;
    
     /**
     * 
     * @ORM\ManyToMany(targetEntity="Chambre", inversedBy="reservations")
     *
    */
    private $chambres;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Occupant", mappedBy="reservation")
     *
    */
    private $occupants;
  
    
    /**
     * 
     * @Assert\True(message="La date d'arrivée est supérieure ou égale à celle de départ")
     * @return boolean
     *
    */
    public function isDatesArriveeDepartValides(){
        return ($this->dateDepart>$this->dateArrivee) ? true: false;
    }
    
    /**
     * 
     * @Assert\True(message="La date d'arrivée est supérieure ou égale à celle de création")
     * @return boolean
     *
    */
    public function isDatesArriveeCreationValides(){
        return ($this->dateArrivee>=$this->dateCreation) ? true: false;
    }
    
    /**
     * vérifie que la date de chechkin est supérieure à la date d'arrivée
     * @Assert\True(message="La date de checkin est inférieure à la date d'arrivée")
     * @return boolean
     *
    */
    public function isDatesArriveeCheckInValides(){
        if (is_null($this->dateCheckIn)) return true;
        return ($this->dateCheckIn>=$this->dateArrivee) ? true: false;
    }
    
    /**
     * vérifie que la date d'option est inférieure ou égale à la date d'arrivée
     * @Assert\True(message="La date d'option est supérieure à la date d'arrivée")
     * @return boolean
     *
    */
    public function isDatesArriveeOptionValides(){
        return ($this->dateOption<=$this->dateArrivee) ? true: false;
    }
    
    /**
     * vérifie que la date de checkin est inférieure  à la date de checkout
     * @Assert\True(message="La date de checkin est supérieure à la date de checkout")
     * @return boolean
     *
    */
    public function isDatesCheckInCheckOutValides(){
        if ((is_null($this->dateCheckIn))||(is_null($this->dateCheckOut))) return true;
        return ($this->dateCheckIn<$this->dateCheckOut) ? true: false;
    }
    
    public function __construct()
    {
    $this->paiements = new \Doctrine\Common\Collections\ArrayCollection();
    $this->reservationOptionRes = new \Doctrine\Common\Collections\ArrayCollection();
    $this->chambres = new \Doctrine\Common\Collections\ArrayCollection();
    $this->occupants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set dateCreation
     *
     * @param datetime $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * Get dateCreation
     *
     * @return datetime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set etat
     *
     * @param string $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * Get etat
     *
     * @return string 
     */
    public function getEtat()
    {
        return $this->etat;
    }
    
     /**
     * Set reduction
     *
     * @param int $reduction
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;
    }

    /**
     * Get reduction
     *
     * @return int 
     */
    public function getReduction()
    {
        return $this->reduction;
    }
    
    
    /**
     * Set dateArrivee
     *
     * @param date $dateArrivee
     */
    public function setDateArrivee($dateArrivee)
    {
        $this->dateArrivee = $dateArrivee;
    }

    /**
     * Get dateArrivee
     *
     * @return date 
     */
    public function getDateArrivee()
    {
        return $this->dateArrivee;
    }

    /**
     * Set dateDepart
     *
     * @param date $dateDepart
     */
    public function setDateDepart($dateDepart)
    {
        $this->dateDepart = $dateDepart;
    }

    /**
     * Get dateDepart
     *
     * @return date 
     */
    public function getDateDepart()
    {
        return $this->dateDepart;
    }

    /**
     * Set heureArrivee
     *
     * @param time $heureArrivee
     */
    public function setHeureArrivee($heureArrivee)
    {
        $this->heureArrivee = $heureArrivee;
    }

    /**
     * Get heureArrivee
     *
     * @return time 
     */
    public function getHeureArrivee()
    {
        return $this->heureArrivee;
    }

    /**
     * Set dateCheckIn
     *
     * @param datetime $dateCheckIn
     */
    public function setDateCheckIn($dateCheckIn)
    {
        $this->dateCheckIn = $dateCheckIn;
    }

    /**
     * Get dateCheckIn
     *
     * @return datetime 
     */
    public function getDateCheckIn()
    {
        return $this->dateCheckIn;
    }

    /**
     * Set dateCheckOut
     *
     * @param datetime $dateCheckOut
     */
    public function setDateCheckOut($dateCheckOut)
    {
        $this->dateCheckOut = $dateCheckOut;
    }

    /**
     * Get dateCheckOut
     *
     * @return datetime 
     */
    public function getDateCheckOut()
    {
        return $this->dateCheckOut;
    }

    /**
     * Set dateOption
     *
     * @param datetime $dateOption
     */
    public function setDateOption($dateOption)
    {
        $this->dateOption = $dateOption;
    }

    /**
     * Get dateOption
     *
     * @return datetime 
     */
    public function getDateOption()
    {
        return $this->dateOption;
    }

    /**
     * Set commentaires
     *
     * @param string $commentaires
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
    }

    /**
     * Get commentaires
     *
     * @return string 
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Set annulation
     *
     * @param Resanet\MainBundle\Entity\Annulation $annulation
     */
    public function setAnnulation(\Resanet\MainBundle\Entity\Annulation $annulation)
    {
        $this->annulation = $annulation;
    }

    /**
     * Get annulation
     *
     * @return Resanet\MainBundle\Entity\Annulation 
     */
    public function getAnnulation()
    {
        return $this->annulation;
    }

    /**
     * Set client
     *
     * @param Resanet\MainBundle\Entity\Client $client
     */
    public function setClient(\Resanet\MainBundle\Entity\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get client
     *
     * @return Resanet\MainBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add paiements
     *
     * @param Resanet\MainBundle\Entity\Paiement $paiements
     */
    public function addPaiement(\Resanet\MainBundle\Entity\Paiement $paiements)
    {
        $this->paiements[] = $paiements;
    }

    /**
     * Get paiements
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPaiements()
    {
        return $this->paiements;
    }

    /**
     * Add reservationOptionRes
     *
     * @param Resanet\MainBundle\Entity\ReservationOptionRes $reservationOptionRes
     */
    public function addReservationOptionRes(\Resanet\MainBundle\Entity\ReservationOptionRes $reservationOptionRes)
    {
        $this->reservationOptionRes[] = $reservationOptionRes;
    }

    /**
     * Get reservationOptionRes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservationOptionRes()
    {
        return $this->reservationOptionRes;
    }

    /**
     * Set coupon
     *
     * @param Resanet\MainBundle\Entity\Coupon $coupon
     */
    public function setCoupon(\Resanet\MainBundle\Entity\Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * Get coupon
     *
     * @return Resanet\MainBundle\Entity\Coupon 
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Add reservationChambres
     *
     * @param Resanet\MainBundle\Entity\ReservationChambre $reservationChambres
     */
    public function addChambre(\Resanet\MainBundle\Entity\Chambre $chambre)
    {
        $this->chambres[] = $chambre;
    }

    /**
     * Get reservationChambres
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChambres()
    {
        return $this->chambres;
    }

    /**
     * Add occupants
     *
     * @param Resanet\MainBundle\Entity\Occupant $occupants
     */
    public function addOccupant(\Resanet\MainBundle\Entity\Occupant $occupants)
    {
        $this->occupants[] = $occupants;
    }

    /**
     * Get occupants
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOccupants()
    {
        return $this->occupants;
    }
    
     public function toArray(){
        $reservation=array();
        $reservation['id']=$this->id;
        $reservation['date']=$this->dateCreation->format('d-m-Y');
        $reservation['dateDebut']=$this->dateArrivee->format('d-m-Y');
        $reservation['dateFin']=$this->dateDepart->format('d-m-Y');
        $reservation['nomClient']=$this->client->getNom().' '.$this->client->getPrenom();
        $reservation['etat']=$this->etat;
        $chbre='';
        foreach($this->getChambres() as $chambre){
            $chbre=$chbre.$chambre->getNom().'-';
        }
        $reservation['chambres']=$chbre;
        $reservation['total']=$this->getTotal();
        $reservation['solde']=$this->getSolde();
        $reservation['form']='<input type="radio" id="'.$this->id.'" name="radio" />';
        return $reservation;
    }
    
    public function enChaine(){
        return 'Réservation n°'.$this->id;
    }
    
    
    /**
     * retourne le nombre de nuits d'une réservation
     * @return int
     */
    public function getNbNuits(){
        //corrige un bug de windows
    if( $this->dateDepart->diff($this->dateDepart)->format("%a") != 6015 ) {
    return $this->dateDepart->diff($this->dateArrivee)->format("%a");
    }
    $y1 = $this->dateDepart->format('Y');  
    $y2 = $this->dateArrivee->format('Y');
    $z1 = $this->dateDepart->format('z');
    $z2 = $this->dateArrivee->format('z');
    $diff = intval($y1 * 365.2425 + $z1) - intval($y2 * 365.2425 + $z2);
      return $diff;
   }
   
   
    /**
     * retourne le montant total d'une réservation en tenant compte des réductions personnels,
     * des coupons de réduction et des prix
     * @return float
     */
    public function getTotal(){
        if($this->annulation){
           $total=$this->annulation->getMontant();
        } else {
            $total=0;
            $total+=$this->getTotalChambres();
            $total-=$this->getTotalReductions();
            $total+=$this->getTotalOptions();
        }
        return $total;
    }
    
     /**
     * retourne la valeur de la retenue sur les arrhes
     * @return float
     */
    public function getRetenueArrhes(){
        if( $this->dateArrivee->diff($this->annulation->getDate())->format("%a") != 6015 ) {
            $nbJours =  intval($this->dateArrivee->diff($this->annulation->getDate()->format("%a")));
        } else {
            $y2 = $this->annulation->getDate()->format('Y');  
            $y1 = $this->dateArrivee->format('Y');
            $z2 = $this->annulation->getDate()->format('z');
            $z1 = $this->dateArrivee->format('z');
            $nbJours = intval($y1 * 365.2425 + $z1) - intval($y2 * 365.2425 + $z2);
       }
       
            if ($nbJours>=30) $retenue=0;
            if (($nbJours<30)&&($nbJours>8)) $retenue=50;
            if ($nbJours<8) $retenue=100;
            return $retenue;
        }

    
    /**
     * retourne le montant total des chambres
     * @return float
     */
    public function getTotalChambres(){
        $total=0;
       foreach($this->getChambres() as $chambre){
            $total+=$chambre->getCategorie()->calculPrixPer($this->dateArrivee->format('Y-m-d'), $this->dateDepart->format('Y-m-d'), $this->dateCreation);
        }
       return $total;
    }
    
    /**
     * retourne le montant total des options
     * @return float
     */
    public function getTotalOptions(){
        $total=0;
       foreach($this->reservationOptionRes as $options){
            $total+=$options->getQuantite()*$options->getOptionRes()->getPrix();
        }
       return $total;
    }
    
    /**
     * retourne le montant total des remises (coupon et réduction personnel)
     * @return float
     */
    public function getTotalReductions(){
        $reduction=0;
        if (!(is_null($this->coupon))){
            if (is_null($this->coupon->getMontant())) $reduction=$this->getTotalChambres()*($this->coupon->getPourcentage())/100;
            else $reduction=$this->coupon->getMontant();
        }
        if (!(is_null($this->getReduction()))) $reduction+=$this->getReduction();
        return $reduction;
    }
    
    
    /**
     * retourne le solde de la réservation
     * @return float
     */
    public function getSolde(){
        return round($this->getTotal()-$this->getTotalPaye(),2);
    }
    
    /**
     * retourne le montant des réductions
     * @return float
     */
    public function getTotalPaye(){
        $totalPaye=0;
        foreach ($this->paiements as $paiement){
            $totalPaye+=$paiement->getMontant();
        }
        return $totalPaye;
    }
    
    /**
     * verifie l'état de la réservation par rapport aux dates de fin d'option, au paiement, aux arrhes
     * à la durée d'options
     * En cours : la personne est arrivée (check in fait)
     * Option : pas d'arrhes et pas encore arrivée
     * Confirmée : arrhes et pas encore arrivée
     * Terminée : check in et checkout
     * Soldée : check in et checkout et payée
     * Annulée : 
     * @param int, float
     * @return void
     */
    public function checkEtat($arrhes){
         if($this->getEtat()!="Annulée"){ 
           $total=$this->getTotal();
           $totalPaye=$this->getTotalPaye();
           if (($totalPaye >=(($arrhes*$total)/100))){
              $this->setEtat('Confirmée');
           } else {$this->setEtat('Option');}
           if (($this->dateCheckIn)&&(!($this->dateCheckOut))){
               $this->setEtat('En cours');
           }
           if (($this->dateCheckIn)&&(($this->dateCheckOut))){
               $this->setEtat('Terminée');
               if ($totalPaye==$total) $this->setEtat('Soldée');
           }
           
           $date=new \DateTime(date('Y-m-d'));
           //$date->setTime(01, 00);
           if (($this->getEtat()=='Option')&&($this->dateOption<$date)){
               $this->setEtat('Annulée');
           }
           if (!($this->dateCheckIn)&&(!($this->dateCheckOut))&&($this->dateDepart==$date)){
               $this->setEtat('Annulée');
           }
        }
    }
    
    
}