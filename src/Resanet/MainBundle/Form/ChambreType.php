<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('nom','text', array('label'=>'Nom'))
                ->add('etage', 'text', array('label'=>'Etage', 'required'=>false))
                ->add('categorie','entity', array('class'=>'ResanetMainBundle:Categorie', 'label'=>'Catégorie', 'property'=>'nom'));
     
    }
    
    public function getName()
    {
        return 'chambre';
    }
}
