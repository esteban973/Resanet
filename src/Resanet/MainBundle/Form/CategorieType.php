<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('nom', 'text',array('label'=>'Categorie'));
        $builder->add('nbLit1', 'integer',array('label'=>'Nombre de lits 1p'));
        $builder->add('nbLit2', 'integer',array('label'=>'Nombre de lits 2p'));
        $builder->add('description', 'textarea',array('label'=>'Description'));
        $builder->add(  'optionsCats', 
                        'entity',
                        array(  'label'=>'Option',
                                'class'=>'ResanetMainBundle:OptionsCat',
                                'property'=>'nom',
                                'multiple'=>true,
                                'expanded'=>true));
        
    }
    
    public function getName()
    {
        return 'categorie';
    }
}
