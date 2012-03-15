<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\OptionRes
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OptionRes
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
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var decimal $prix
     *
     * @ORM\Column(name="prix", type="decimal", scale="2")
     * @Assert\Min(limit=0, message="Pas de prix négatif")
     * @Assert\Max(limit=150, message="La limite est de 150€")
     */
    private $prix;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\MaxLength(limit=255, message="Pas de description trop longue")
     * @Assert\MinLength(limit=5, message="Pas de description trop courte")
     */
    private $description;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="ReservationOptionRes", mappedBy="optionRes")
     *
    */
    private $reservationOptionRes;


    public function __construct()
    {
        $this->reservationOptionRes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set prix
     *
     * @param decimal $prix
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    /**
     * Get prix
     *
     * @return decimal 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
    
    public function enChaine(){
         return 'Option :'.$this->nom.' Prix :'.$this->prix.' €';
       }
    
    
    public function toArray(){
        $option=array();
        $option['id']=$this->id;
        $option['nom']=$this->nom;
        $option['description']=$this->description;
        $option['prix']=$this->prix.' €';
        $option['form']='<input type="radio" id="'.$this->id.'" name="radio" />';
        return $option;
    }
    
}