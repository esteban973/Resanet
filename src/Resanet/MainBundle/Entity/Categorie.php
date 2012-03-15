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
    * Resanet\MainBundle\Entity\Categorie
    *
    * @ORM\Table()
    * @ORM\Entity
    * @property integer $id
    * @property string $nom
    * @property integer $nbLit1
    * @property integer $nbLit2
    * @property string $description
      * @property Doctrine\Common\Collections\Collection $periodeCategories
     * @property Doctrine\Common\Collections\Collection $optionsCat
     * @property Doctrine\Common\Collections\Collection $reductions
     * @property Doctrine\Common\Collections\Collection $chambres
      * @property string $image
    */

class Categorie
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
     * 
     * @Assert\NotBlank(message="Le nom doit être renseigné")
     * @ORM\Column(name="nom", type="string", length=255, unique="true")
     */
    private $nom;

    /**
     * @var integer $nbLit1
     * @Assert\Min(limit = 0, message = "Le nombre de lits doit être supérieur ou égal à 0")
     * @Assert\Max(limit = 3, message = "Le nombre de lits doit être inférieur à 4")
     * @ORM\Column(name="nbLit1", type="integer")
     */
    private $nbLit1=0;

    /**
     * @var integer $nbLit2
     * @Assert\Min(limit = 0, message = "Le nombre de lits doit être supérieur ou égal à 0")
     * @Assert\Max(limit = 3, message = "Le nombre de lits doit être inférieur à 4")
     * @ORM\Column(name="nbLit2", type="integer")
     */
    private $nbLit2=0;

      
    /**
     * @var string $description
     * @Assert\NotBlank(message="Le nom doit être renseigné")
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var Doctrine\Common\Collections\Collection $periodeCategories
     * @ORM\OneToMany(targetEntity="PeriodeCategorie", mappedBy="categorie", cascade={"remove"})
     */
    private $periodeCategories;
    
    /**
     * @var Doctrine\Common\Collections\Collection $optionsCat
     * @ORM\ManyToMany(targetEntity="OptionsCat", inversedBy="categories")
     */
    private $optionsCats;
    
    /**
     * @var Doctrine\Common\Collections\Collection $reductions
     * @ORM\ManyToMany(targetEntity="Reduction", mappedBy="categories")
     */
    private $reductions;
    
    /**
     * @var Doctrine\Common\Collections\Collection $chambres
     * @ORM\OneToMany(targetEntity="Chambre", mappedBy="categorie")
     */
    private $chambres;
    
    /**
     * @var string $image
     * @ORM\Column(name="image", type="string", length=255, nullable="true")
     */
    private $image;
    
    /**
     * Constructeur
     * @return Categorie
     */
    
    public function __construct()
    {
        $this->periodeCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->optionsCats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reductions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->chambres = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * vérifie si la chambre possède au moins un lit
     * @Assert\True(message = "Vous devez renseigner un nombre de lits")
     * @return boolean
     */
    function isNbreLitsDefined() {
        if ( !(isset($this->nbLit1)) && !(isset($this->nbLit2)))
        {
            return false;
        }
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
     * Set nbLit1
     *
     * @param integer $nbLit1
     */
    public function setNbLit1($nbLit1)
    {
        $this->nbLit1 = $nbLit1;
    }

    /**
     * Get nbLit1
     * @return integer 
     */
    public function getNbLit1()
    {
        return $this->nbLit1;
    }

    /**
     * Set nbLit2
     * @param integer $nbLit2
     */
    public function setNbLit2($nbLit2)
    {
        $this->nbLit2 = $nbLit2;
    }

    /**
     * Get nbLit2
     * @return integer 
     */
    public function getNbLit2()
    {
        return $this->nbLit2;
    }

    /**
     * Set description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set description
     * @param string $description
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get description
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }
       
    /**
     * Ajoute periodeCategories
     * @param PeriodeCategorie
     */
    public function addPeriodeCategorie(PeriodeCategorie $periodeCategories)
    {
        $this->periodeCategories[] = $periodeCategories;
    }

    /**
     * Retourne periodeCategories
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPeriodeCategories()
    {
        return $this->periodeCategories;
    }

    /**
     * Ajoute une option à la catégorie
     * @param OptionsCats
     */
    public function addOptionsCat(OptionsCat $optionsCats)
    {
        $this->optionsCats[] = $optionsCats;
    }

    /**
     * Retourne les options de la catégorie
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOptionsCats()
    {
        return $this->optionsCats;
    }

    /**
     * Ajoute des reductions
     * @param Reduction
     */
    public function addReduction(Reduction $reductions)
    {
        $this->reductions[] = $reductions;
    }

    /**
     * Retourne les reductions
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReductions()
    {
        return $this->reductions;
    }

    /**
     * Ajoute chambres
     * @param Chambre
     */
    public function addChambre(Chambre $chambres)
    {
        $this->chambres[] = $chambres;
    }

    /**
     * retourne collection Chambres
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChambres()
    {
        return $this->chambres;
    }
    
   /**
     * renvoie un tableau qui servirait à alimenter la dataTable de la vue Categorie
     * @return array
     */
    public function toArray() {
        $cat = array();
        $cat['id'] = $this->id;
        $cat['nom'] = $this->nom;
        $cat['image'] = '<img src="/resanet/web/uploader/img/thumbnail/'.$this->image.'" />';
        $cat['description'] = $this->description;
        return $cat;
    }
    
    
    
    /**
     * une fonction toString
     * @return string
     */
    public function enChaine(){
        return 'Catégorie :'.$this->nom.' '.$this->nbLit1.'lit(s) 1p, '.$this->nbLit2.'lit(s) 2p';
    }
    
    
    /**
     * fonction pour savoir si le prix d'une catégorie est défini pour une date et donc pour une réservation
     * @param DateTime
     * @return boolean
     */
    public function isPrixDef($date){
        foreach ($this->periodeCategories as $periodeCat){
            if (($date>=$periodeCat->getPeriode()->getDateDebut())&&($date<=$periodeCat->getPeriode()->getDateFin())){
                return true;
            }
        }
        return false;
    }
    
    /**
     * fonction pour savoir si le prix d'une catégorie est défini pour une période et donc pour une réservation
     * @param string(Y-m-d)
     * @param string(Y-m-d)
     * @return boolean
     */
   
    public function isPrixDefPer($date1, $date2){
       $date1=new \DateTime($date1);
       $date2=new \DateTime($date2);
       while ($date1 < $date2){
           if ($this->isPrixDef($date1)===false){
              return false;
           }
           $date1=$date1->add(new \DateInterval('P1D'));
       }
       return true;
    }
    
     /**
     * function qui permet de calucler le prix d'une chambre d'une catégorie à une date donnée (en tenant compte des 
     * éventuelles réductions). Les calucls de prix tiennent compte soit du prix du jour de création de la réservation,
      * soit de la date d'auj. Pour calcul  Aujourd'hui mettre null
     * @param DateTime
      * @param DateTime ou null
     * @return float
     */
    
    public function calculPrix($date, $dateRef){
        $prixUnit=0;
        $pourcent=0;
        $date2=(is_null($dateRef))? new\DateTime("now") : $dateRef;
        foreach ($this->periodeCategories as $periodeCat){
            if (($date>=$periodeCat->getPeriode()->getDateDebut())&&($date<=$periodeCat->getPeriode()->getDateFin())){
                $prixUnit=$periodeCat->getPrix();
            }
        }
        foreach ($this->reductions as $reduc){
             if (($date>=$reduc->getDateDebPeriod())&&($date<=$reduc->getDateFinPeriod())&&($date2>=$reduc->getDateDebEff())&&($date2<=$reduc->getDateFinEff())){
                $pourcent=$reduc->getPourcent();
             }
        }
        return ($prixUnit*(100-$pourcent)/100);       
    }
    
    
     /**
     * function qui permet de calucler le prix d'une chambre d'une catégorie sur une période donnée (en tenant compte des 
     * éventuelles réductions). Les calucls de prix tiennent compte soit du prix du jour de création de la réservation,
      * soit de la date d'auj. Pour calcul  Aujourd'hui mettre null
     * @param string(Y-m-d)
      * @param string(Y-m-d)
      * @param DateTime ou null
     * @return float
     */
    
    public function calculPrixPer($dateDebut, $dateFin, $dateP){
       $prix=0;
       $date=(is_null($dateP))? new\DateTime("now") : $dateP;
       $dateDebut=new \DateTime($dateDebut);
       $dateFin=new \DateTime($dateFin);
       while ($dateDebut < $dateFin){
           $prix+=$this->calculPrix($dateDebut, $date);
           $dateDebut=$dateDebut->add(new \DateInterval('P1D'));
       }  
       return $prix;
    }
    
    /**
     * callculer le nombre de chambres disponibles sur une période donnée
     * @param string (Y-m-d)
     * @param string (Y-m-d)
     * @return int
     */
    
   
    public function nbChambresDispo($date1, $date2){
        $nbChambres=0;
        foreach($this->getChambres() as $chbre){
            if ($chbre->estDispoPeriode($date1, $date2)) $nbChambres++;
        }
        return $nbChambres;
    }
    
    
    
}