<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('nom', 'text', array('label'=>'Nom'))
                ->add('prenom', 'text', array('label'=>'Prénom'))
                ->add('societe', 'text', array('label'=>'Société'))
                ->add('adresse', 'text', array('label'=>'Adresse'))
                ->add('cp', 'text', array('label'=>'Code Postal'))
                ->add('ville', 'text', array('label'=>'Ville'))
                ->add('pays', 'text', array('label'=>'Pays'))
                ->add('telephone', 'text', array('label'=>'Téléphone'))
                ->add('email', 'text', array('label'=>'Email'))
                ->add('observation', 'textarea', array('label'=>'Observations'));
    }
    
    public function getName()
    {
        return 'client';
    }
}
