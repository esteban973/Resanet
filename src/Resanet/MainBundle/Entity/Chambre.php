<?php
/**
     * @author:Stephane Lanjard stephane.lanjard@gmail.com
     * @package ResanetMainBundle
     * @subpackage Entity
     */
namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Resanet\MainBundle\Entity\Chambre
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Resanet\MainBundle\Entity\ChambreRepository")
 */
class Chambre
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
     * @var string $nom
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string $etage
     * @ORM\Column(name="etage", type="string", length=255, nullable=true)
     */
    private $etage;
    
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection Reservations $reservation
     * @ORM\ManyToMany(targetEntity="Reservation", mappedBy="chambres")
    */
    private $reservations;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection Indispo $indispo
     * @ORM\OneToMany(targetEntity="Indispo", mappedBy="chambre")
    */
    private $indispos;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection Occupant $occupant
     * @ORM\OneToMany(targetEntity="Occupant", mappedBy="chambre")
    */
    private $occupants;
    
    /**
     * @var Categorie $categorie
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="chambres")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id", nullable=false)
    */
    private $categorie;

    /**
     * Constructeur
     * @return Chambre
     */
    public function __construct()
    {
     $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    $this->indispos = new \Doctrine\Common\Collections\ArrayCollection();
    $this->occupants = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * Get nom
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set etage
     * @param string $etage
     */
    public function setEtage($etage)
    {
        $this->etage = $etage;
    }

    /**
     * Get etage
     * @return string 
     */
    public function getEtage()
    {
        return $this->etage;
    }

    /**
     * Add reservations
     * @param Reservation $reservation
     */
    public function addReservation(Reservation $reservation)
    {
        $this->reservations[] = $reservation;
    }

    /**
     * Get reservations
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Add indispos
     * @param Indispo
     */
    public function addIndispo(Indispo $indispos)
    {
        $this->indispos[] = $indispos;
    }

    /**
     * Get indispos
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getIndispos()
    {
        return $this->indispos;
    }

    /**
     * Add occupants
     * @param Occupant
     */
    public function addOccupant(Occupant $occupants)
    {
        $this->occupants[] = $occupants;
    }

    /**
     * Get occupants
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOccupants()
    {
        return $this->occupants;
    }

    /**
     * Set categorie
     * @param Categorie
     */
    public function setCategorie(Categorie $categorie)
    {
        $this->categorie = $categorie;
    }

    /**
     * Get categorie
     * @return Categorie 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
    
    /**
     * une fonction toString
     * @return string
     */
     public function enChaine(){
        return 'Chambre :'.$this->nom.' Etage :'.$this->etage.' Categorie :'.$this->categorie->getNom();
    }
    
    /**
     * renvoie un tableau qui servirait a alimenter la dataTable de la vue Chambre
     * @return array
     */
    public function toArray(){
        $chambre=array();
        $chambre['id']=$this->id;
        $chambre['nom']=$this->nom;
        $chambre['etage']=$this->etage;
        $chambre['categorie']=$this->categorie->getNom();
         return $chambre;
    }
    
    /**
     * pour une date donnee verifie s'il n'y a pas de reservations {@link pasDeReservation()},
     * s'il n'y a pas d'e reservations'indispo 
     * @link pasDIndispo()
     * si le prix de sa categorie est definie 
     * @link pasDeReservation()
     * @param DateTime
     * @return boolean
     */
    
    public function estDispo($date){
        return (($this->pasDeReservation($date))&&($this->pasDIndispo($date))&&($this->getCategorie()->isPrixDef($date)))? true:false;
    }
    
    /**
     * pour 2 dates donnees au format (yyyy-mm-dd), fais une boucle et fais appel a {@link estDispo()} pour verifier la disponibilite
     * @param string
     * @param string
     * @return boolean
     */
    public function estDispoPeriode($date1, $date2){
        $date1=new \DateTime($date1);
        $date2=new \DateTime($date2);
        while($date1<$date2){
             if($this->estDispo($date1)==false) return false;
             $date1=$date1->add(new \DateInterval('P1D'));
        }  
        return true;
    }
    
     /**
     * pour 1 date donnee  verifier s'il y a une reservation en cours ou pas (si reservation return false)
     * @param DateTime
     * @return boolean
     */
    public function pasDeReservation($date){
        foreach($this->getReservations() as $res){
            if (($res->getEtat()!='Annulée')&&($res->getEtat()!='Soldée')&&($date>=$res->getDateArrivee())&&($date<$res->getDateDepart())){
                return false;
            }
        } 
        return true;
    }
    
    
    /**
     * pour 2 dates donnees au format (yyyy-mm-dd), fais une boucle et fais appel a {@link pasDeReservation()} 
     * pour verifier s'il y a ou pas une reservation (si reservation return false)
     * @param string
     * @param string
     * @return boolean
     */
    public function pasDeReservationPeriode($date1, $date2){
        $date1=new \DateTime($date1);
        $date2=new \DateTime($date2);
        while($date1<$date2){
             if($this->pasDeReservation($date1)==false) return false;
             $date1=$date1->add(new \DateInterval('P1D'));
        }  
        return true;
    }
    
    /**
     * pour 1 date donnee  verifier s'il y a une reservation en cours ou pas (si reservation return false)
     * sans compter la reservation passee en arguments
     * @param DateTime
     * @param Reservation
     * @return boolean
     */
    public function pasDeReservationHormisRes($date, $reservation){
        foreach($this->getReservations() as $res){
            if (($res!=$reservation)&&($res->getEtat()!='Annulée')&&($date>=$res->getDateArrivee())&&($date<$res->getDateDepart())){
                return false;
            }
        } 
        return true;
    }
       
    
    /**
     * pour 2 dates donnees au format (yyyy-mm-dd), fais une boucle et fais appel a {@link pasDeReservationHormisRes()} 
     * pour verifier s'il y a ou pas une reservation sans compter la reservation passee en arguments(si reservation return false)
     * @param string
     * @param string
     * @param  Reservation
     * @return boolean
     */
    public function pasDeReservationPeriodeHormisRes($date1, $date2, $reservation){
        $date1=new \DateTime($date1);
        $date2=new \DateTime($date2);
        while($date1<$date2){
             if($this->pasDeReservationHormisRes($date1, $reservation)==false) return false;
             $date1=$date1->add(new \DateInterval('P1D'));
        }  
        return true;
    }
    
    /**
     * pour une date donnee verifie s'il n'y a pas de reservations {@link pasDeReservation()},
     * s'il n'y a pas d'e reservations'indispo {@link pasDIndispo()},
     * si le prix de sa categorie est definie {@link pasDeReservation()}
     * @param DateTime
     * @return boolean
     */
    
    public function estDispoHormisRes($date, $reservation){
        return (($this->pasDeReservationHormisRes($date, $reservation))&&($this->pasDIndispo($date))&&($this->getCategorie()->isPrixDef($date)))? true:false;
    }
    
    /**
     * pour 2 dates donnees au format (yyyy-mm-dd), fais une boucle et fais appel a {@link estDispo()} pour verifier la disponibilite
     * @param string
     * @param string
     * @return boolean
     */
    public function estDispoPeriodeHormisRes($date1, $date2, $reservation){
        $date1=new \DateTime($date1);
        $date2=new \DateTime($date2);
        while($date1<$date2){
             if($this->estDispoHormisRes($date1, $reservation)==false) return false;
             $date1=$date1->add(new \DateInterval('P1D'));
        }  
        return true;
    }
    
    
    /**
     * pour 2 dates donnees au format (yyyy-mm-dd), fais une boucle et fais appel a {@link pasDeReservation()} 
     * pour verifier s'il y a ou pas une reservation (si reservation return false)
     * @param string
     * @param string
     * @return boolean
     */
    public function pasDIndispoPeriode($date1, $date2){
        $date1=new \DateTime($date1);
        $date2=new \DateTime($date2);
        while($date1<$date2){
             if($this->pasDIndispo($date1)==false) return false;
             $date1=$date1->add(new \DateInterval('P1D'));
        }  
        return true;
    }
    
    
   /**
     * pour 1 date donnee  verifier s'il y a une indispo en cours ou pas (si indispo return false)
     * @param dateTime
     * @return boolean
     */
    public function pasDIndispo($date){
       foreach($this->getIndispos() as $indispo){
            if (($date>=$indispo->getDateDebut())&&($date<=$indispo->getDateFin())){
                return false;
            }
        } 
        return true;
    }
    
    
    /**
     * pour 1 date donnee  retourne la reservation sur l'objet Chambre. Retourne null si pas de reservation
     * @param dateTime
     * @return Reservation
     */
    public function correspondReservation($date){
        foreach($this->getReservations() as $res){
           if (($res->getEtat()!='Annulée')&&($date>=$res->getDateArrivee())&&($date<$res->getDateDepart())){
                return $res;
            }
        }
        return null;
    }
    
    
}