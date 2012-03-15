<?php

namespace Resanet\UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('nomUtilisateur', 'text', array('label'=>'Nom'))
                ->add('prenomUtilisateur', 'text', array('label'=>'Prénom'))
                ->add('passWordUtilisateur', 'password', array('label'=>'Mot de passe'))
                ->add('identifiant','text', array('label'=>'Identifiant'))
                ->add('fonctionUtilisateur','choice', array('label'=>'Fonction', 'choices'=>array('personnel'=>'personnel', 'gestionnaire'=>'gestionnaire') ));
     
    }
    
    public function getName()
    {
        return 'utilisateur';
    }
}
