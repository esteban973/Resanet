<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PeriodeCategorieType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('prix', 'text',array('label'=>'Prix'));
        
    }
    
    public function getName()
    {
        return 'periodeCat';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Resanet\MainBundle\Entity\PeriodeCategorie',
        );
    }
    
}
