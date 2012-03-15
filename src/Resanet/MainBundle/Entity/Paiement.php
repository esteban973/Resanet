<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\Paiement
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Paiement
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
     * @var decimal $montant
     *
     * @ORM\Column(name="montant", type="decimal", scale="2")
     * @Assert\Max(limit=1000, message="Le montant ne peut excéder {{limit}}")
     * @Assert\NotNull(message="Vous devez renseigner le montant")
     */
    private $montant;

    /**
     * @var datetime $datetime
     *
     * @ORM\Column(name="datePaiement", type="datetime")
     * @Assert\Datetime(message="La date de paiement n'est pas au bon format")
     * @Assert\NotNull(message="Vous devez renseigner la date de paiement")
     */
    private $datePaiement;

    /**
     * @var string $moyen
     *
     * @ORM\Column(name="moyen", type="string", length=255)
     * @Assert\NotNull(message="Le moyen de paiement doit être indiqué")
     * @Assert\Choice(choices={"PayPal","Chèque","Espèces","Carte Bleue", "Chèque ANCV"})
     */
    private $moyen;

    /**
     *  
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="paiements")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id", nullable=false)
     */
    private $reservation;
    

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
     * Set montant
     *
     * @param decimal $montant
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
     * Set datePaiement
     *
     * @param datetime $datePaiement
     */
    public function setDatePaiement($datePaiement)
    {
        $this->datePaiement = $datePaiement;
    }

    /**
     * Get datePaiement
     *
     * @return datetime 
     */
    public function getDatePaiement()
    {
        return $this->datePaiement;
    }

    /**
     * Set moyen
     *
     * @param string $moyen
     */
    public function setMoyen($moyen)
    {
        $this->moyen = $moyen;
    }

    /**
     * Get moyen
     *
     * @return string 
     */
    public function getMoyen()
    {
        return $this->moyen;
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