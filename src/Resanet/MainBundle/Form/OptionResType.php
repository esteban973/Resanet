<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class OptionResType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('nom','text', array('label'=>'Nom'))
                ->add('description', 'textarea', array('label'=>'Description', 'required'=>false))
                ->add('prix','number', array('label'=>'Prix unitaire'));
    }
    
    public function getName()
    {
        return 'optionRes';
    }
}
