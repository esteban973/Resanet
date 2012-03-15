<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\ReservationOptionRes
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ReservationOptionRes
{
   

        /**
     * @var integer $quantite
     *
     * @ORM\Column(name="quantite", type="integer")
     * @Assert\Max(limit=100, message="Le nombre maximal est 100 pour la quantité d'option")
     * @Assert\Min(limit=0, message="Le nombre minimal est 0")
     * @Assert\NotBlank(message="Vous devez nécessairement déterminer une quantité")    
     */
    private $quantite;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OptionRes", inversedBy="reservationOptionRes")
     *
    */
    private $optionRes;
    
     /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="reservationOptionRes")
     *
    */
    private $reservation;
    
    
    

    /**
     * Set quantite
     *
     * @param integer $quantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    /**
     * Get quantite
     *
     * @return integer 
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set optionRes
     *
     * @param Resanet\MainBundle\Entity\OptionRes $optionRes
     */
    public function setOptionRes(\Resanet\MainBundle\Entity\OptionRes $optionRes)
    {
        $this->optionRes = $optionRes;
    }

    /**
     * Get optionRes
     *
     * @return Resanet\MainBundle\Entity\OptionRes 
     */
    public function getOptionRes()
    {
        return $this->optionRes;
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
}