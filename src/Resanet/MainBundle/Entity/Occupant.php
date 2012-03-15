<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Occupant
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Occupant
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Personne", inversedBy="occupants")
     *
    */
    private $personne;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="occupants")
     *
    */
    private $reservation;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Chambre", inversedBy="occupants")
     *
    */
    private $chambre;



    /**
     * Set personne
     *
     * @param Resanet\MainBundle\Entity\Personne $personne
     */
    public function setPersonne(\Resanet\MainBundle\Entity\Personne $personne)
    {
        $this->personne = $personne;
    }

    /**
     * Get personne
     *
     * @return Resanet\MainBundle\Entity\Personne 
     */
    public function getPersonne()
    {
        return $this->personne;
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
}