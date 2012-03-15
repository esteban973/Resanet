<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PeriodeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('dateDebut', 'jquery_date',array('label'=>'Date de début','format' => 'dd/MM/y'));
        $builder->add('dateFin', 'jquery_date',array('label'=>'Date de fin','format' => 'dd/MM/y'));
    }
    
    public function getName()
    {
        return 'categorie';
    }
    
     public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Resanet\MainBundle\Entity\Periode',
        );
    }
    
}
