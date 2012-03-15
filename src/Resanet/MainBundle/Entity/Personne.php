<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Personne
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Personne
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
     * @var string $prenom
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @var date $dateNaissance
     *
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateNaissance;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Occupant", mappedBy="personne")
     *
    */
    private $occupants;


    public function __construct()
    {
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
     * Set dateNaissance
     *
     * @param date $dateNaissance
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    }

    /**
     * Get dateNaissance
     *
     * @return date 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
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
}