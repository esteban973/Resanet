<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Annulation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Annulation
{

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotNull()
     */
    private $date;

    /**
     * @var string $raison
     *
     * @ORM\Column(name="raison", type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigné la raison")
     */
    private $raison;
    
     /**
     * @var decimal $montant
     *
     * @ORM\Column(name="montant", type="decimal", scale=2)
     */
    private $montant;
    
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Reservation", inversedBy="annulation")
     * 
     */
    private $reservation;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="annulations")
     */
    private $client;
    

    /**
     * Set montant
     *
     * @param decimal $ montant
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
        
    }

    /**
     * Get montant
     *
     * @return decimal 
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set raison
     *
     * @param string $raison
     */
    public function setRaison($raison)
    {
        $this->raison = $raison;
    }

    /**
     * Get raison
     *
     * @return string 
     */
    public function getRaison()
    {
        return $this->raison;
    }

    /**
     * Set reservation
     *
     * @param Resanet\MainBundle\Entity\Reservation $reservation
     */
    public function setReservation(\Resanet\MainBundle\Entity\Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get reservation
     *
     * @return Resanet\MainBundle\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
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
}