<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class IndispoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('chambre', 'entity', array('label'=>'Chambre', 'class'=>'ResanetMainBundle:Chambre', 'property'=>'nom'))
                ->add('dateDebut', 'jquery_date', array('label'=>'Date de début','format' => 'dd/MM/y'))
                ->add('dateFin', 'jquery_date', array('label'=>'Date de fin','format' => 'dd/MM/y'));
     
    }
    
    public function getName()
    {
        return 'indispo';
    }
}
