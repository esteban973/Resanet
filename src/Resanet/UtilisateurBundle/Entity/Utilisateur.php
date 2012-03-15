<?php

namespace Resanet\UtilisateurBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Resanet\UtilisateurBundle\Entity\Utilisateur
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Utilisateur implements UserInterface
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
     * @var string $identifiant
     *
     * @ORM\Column(name="identifiant", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\MinLength(
     *     limit=7,
     *     message="Votre identifiant doit comporter au minimum 7 caractères")
     * @Assert\MaxLength(
     *     limit=15,
     *     message="Votre identifiant doit comporter au maximum 15 caractères")
     *
     */
    private $identifiant;

    /**
     * @var string $nomUtilisateur
     * @Assert\NotBlank()
     * @ORM\Column(name="nomUtilisateur", type="string", length=255)
     * 
     */
    private $nomUtilisateur;

    /**
     * @var string $prenomUtilisateur
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="prenomUtilisateur", type="string", length=255)
     */
    private $prenomUtilisateur;

    /**
     * @var string $passwordUtilisateur
     *
     * @ORM\Column(name="passwordUtilisateur", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\MinLength(
     *     limit=7,
     *     message="Votre mot de passe doit comporter au minimum 7 caractères")
     * @Assert\MaxLength(
     *     limit=15,
     *     message="Votre mot de passe doit comporter au maximum 15 caractères")
     *
     */
    private $passwordUtilisateur;

    /**
     * @var string $fonctionUtilisateur
     *@Assert\NotBlank()
     * @ORM\Column(name="fonctionUtilisateur", type="string", length=255)
     * 
     */
    private $fonctionUtilisateur;

    /**
     * @ORM\Column(name="salt", type="string", length=40)
     */
     private $salt;
     
     public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
     * Set identifiant
     *
     * @param string $identifiant
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
    }

    /**
     * Get identifiant
     *
     * @return string 
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * Set nomUtilisateur
     *
     * @param string $nomUtilisateur
     */
    public function setNomUtilisateur($nomUtilisateur)
    {
        $this->nomUtilisateur = $nomUtilisateur;
    }

    /**
     * Get nomUtilisateur
     *
     * @return string 
     */
    public function getNomUtilisateur()
    {
        return $this->nomUtilisateur;
    }

    /**
     * Set prenomUtilisateur
     *
     * @param string $prenomUtilisateur
     */
    public function setPrenomUtilisateur($prenomUtilisateur)
    {
        $this->prenomUtilisateur = $prenomUtilisateur;
    }

    /**
     * Get prenomUtilisateur
     *
     * @return string 
     */
    public function getPrenomUtilisateur()
    {
        return $this->prenomUtilisateur;
    }

    /**
     * Set passwordUtilisateur
     *
     * @param string $passwordUtilisateur
     */
    public function setPasswordUtilisateur($passwordUtilisateur)
    {
        $this->passwordUtilisateur = $passwordUtilisateur;
    }

    /**
     * Get passwordUtilisateur
     *
     * @return string 
     */
    public function getPasswordUtilisateur()
    {
        return $this->passwordUtilisateur;
    }

    /**
     * Set fonctionUtilisateur
     *
     * @param string $fonctionUtilisateur
     */
    public function setFonctionUtilisateur($fonctionUtilisateur)
    {
        $this->fonctionUtilisateur = $fonctionUtilisateur;
    }

    /**
     * Get fonctionUtilisateur
     *
     * @return string 
     */
    public function getFonctionUtilisateur()
    {
        return $this->fonctionUtilisateur;
    }
    
    public function enChaine(){
        return 'Utilisateur : '.$this->identifiant.' Nom :'.$this->nomUtilisateur.' Prénom :'.$this->prenomUtilisateur.' Fonction :'.$this->fonctionUtilisateur;
    }
    
    public function toArray(){
        $utilisateur=array();
        $utilisateur['id']=$this->id;
        $utilisateur['identifiant']=$this->identifiant;
        $utilisateur['nomUtilisateur']=$this->nomUtilisateur.' '.$this->prenomUtilisateur;
        $utilisateur['fonctionUtilisateur']=$this->fonctionUtilisateur;
        return $utilisateur;
    }

    public function equals(UserInterface $user) {
        return $user->getUsername() === $this->identifiant;
    }

    public function eraseCredentials() {
        
    }

    public function getPassword() {
        return $this->passwordUtilisateur;
    }

    public function getRoles() {
        $fonction=$this->fonctionUtilisateur;
        switch ($fonction){
            case 'personnel': $role=array('ROLE_ADMIN'); break;
            case 'gestionnaire': $role=array('ROLE_SUPER_ADMIN'); break;
        }
        return $role;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->identifiant;
    }
}