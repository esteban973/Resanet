<?php

namespace Resanet\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resanet\MainBundle\Entity\PeriodeCategorie
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PeriodeCategorie
{
    /**
     * @var decimal $prix
     *
     * @ORM\Column(name="prix", type="decimal", scale=2)
     */
    private $prix;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Periode", inversedBy="periodeCategories")
     */
    private $periode;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="periodeCategories")
     */
    private $categorie;
    
   

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
     * Set periode
     *
     * @param Resanet\MainBundle\Entity\Periode $periode
     */
    public function setPeriode(\Resanet\MainBundle\Entity\Periode $periode)
    {
        $this->periode = $periode;
    }

    /**
     * Get periode
     *
     * @return Resanet\MainBundle\Entity\Periode 
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set categorie
     *
     * @param Resanet\MainBundle\Entity\Categorie $categorie
     */
    public function setCategorie(\Resanet\MainBundle\Entity\Categorie $categorie)
    {
        $this->categorie = $categorie;
    }

    /**
     * Get categorie
     *
     * @return Resanet\MainBundle\Entity\Categorie 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}