<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Client
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Client
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
     * @var string $nomClient
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank(message="Le nom n'est pas renseigné")
     */
    private $nom;

    /**
     * @var string $prenom
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank(message="Le prénom n'est pas renseigné")
     */
    private $prenom;

    /**
     * @var string $societe
     *
     * @ORM\Column(name="societe", type="string", length=255, nullable="true")
     */
    private $societe;

    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     * @Assert\NotBlank(message="L'adresse n'est pas renseignée")
     */
    private $adresse;

    /**
     * @var string $ville
     *
     * @ORM\Column(name="ville", type="string", length=255)
     * @Assert\NotBlank(message="La ville n'est pas renseignée")
     */
    private $ville;

    /**
     * @var string $cp
     *
     * @ORM\Column(name="cp", type="string", length=255)
     * @Assert\NotBlank(message="Le code postal n'est pas renseigné")
     */
    private $cp;

    /**
     * @var string $pays
     *
     * @ORM\Column(name="pays", type="string", length=255)
     * @Assert\NotBlank(message="Le pays n'est pas renseigné")
     */
    private $pays;

    /**
     * @var string $telephone
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Assert\NotBlank(message="Le téléphone n'est pas renseigné")
     */
    private $telephone;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(message = "L'email '{{ value }}' ne semble pas être valide.", checkMX = true)
     */
    private $email;

    /**
     * @var string $observation
     *
     * @ORM\Column(name="observation", type="string", length=255, nullable=true)
     */
    private $observation;

    
     /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="client")
     */
    private $reservations;
    
     /**
     * @ORM\OneToMany(targetEntity="Annulation", mappedBy="client")
     */
    private $annulations;
    
    
     /**
     * @Assert\True(message="Le client est injoignable !!!")
     */
    public function isJoignable(){
        if (is_null($this->telephone)&&is_null($this->email)){
            return false;
        } else return true;
    }
    
    
    
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    $this->annulations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set societe
     *
     * @param string $societe
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;
    }

    /**
     * Get societe
     *
     * @return string 
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set ville
     *
     * @param string $ville
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set cp
     *
     * @param string $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * Get cp
     *
     * @return string 
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set pays
     *
     * @param string $pays
     */
    public function setPays($pays)
    {
        $this->pays = $pays;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }


    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set observation
     *
     * @param string $observation
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;
    }

    /**
     * Get observation
     *
     * @return string 
     */
    public function getObservation()
    {
        return $this->observation;
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

    /**
     * Add annulations
     *
     * @param Resanet\MainBundle\Entity\Annulation $annulations
     */
    public function addAnnulation(\Resanet\MainBundle\Entity\Annulation $annulations)
    {
        $this->annulations[] = $annulations;
    }

    /**
     * Get annulations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAnnulations()
    {
        return $this->annulations;
    }
    
    public function enChaine(){
        return 'Client :'.$this->nom.' '.$this->prenom.' Ville :'.$this->ville.', '.$this->pays;
    }
    
    public function toArray(){
        $client=array();
        $client['id']=$this->id;
        $client['nom']=$this->nom.' '.$this->prenom;
        $client['societe']=(isset($this->societe))? $this->societe : '-';
        $client['telephone']=$this->telephone ;
        $client['email']=(isset($this->email))? $this->email : '-';
         $client['adresse']=$this->adresse ;
        $client['ville']=$this->ville.', '.$this->pays;
        
        return $client;
    }
    
}