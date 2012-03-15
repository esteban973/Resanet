<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ReductionType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('libelle','text', array('label'=>'Nom'))
                ->add('pourcent','number', array('label'=>'Pourcentage', 'required'=>false))
                ->add('dateDebEff', 'jquery_date', array('label'=>'Date de début','format' => 'dd/MM/y'))
                ->add('dateFinEff', 'jquery_date', array('label'=>'Date de fin','format' => 'dd/MM/y'))
                ->add('dateDebPeriod', 'jquery_date', array('label'=>'Début de période','format' => 'dd/MM/y'))
                ->add('dateFinPeriod', 'jquery_date', array('label'=>'Fin de période','format' => 'dd/MM/y'))
                ->add('categories','entity',array('class'=>'ResanetMainBundle:Categorie', 'label'=>'Catégorie', 'property'=>'nom','multiple'=>true, 'expanded'=>true));
                
    }
    
    public function getName()
    {
        return 'reduction';
    }
}
