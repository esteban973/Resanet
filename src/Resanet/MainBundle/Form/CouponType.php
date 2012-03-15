<?php

namespace Resanet\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CouponType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('codepromo','text', array('label'=>'Code Promo'))
                ->add('montant', 'number', array('label'=>'Montant', 'required'=>false))
                ->add('pourcentage','number', array('label'=>'Pourcentage', 'required'=>false))
                ->add('dateDebut', 'jquery_date', array('label'=>'Date de début','format' => 'dd/MM/y'))
                ->add('dateFin', 'jquery_date', array('label'=>'Date de fin','format' => 'dd/MM/y'))
                ->add('nbDebut','number', array('label'=>'Nombre'));
                
    }
    
    public function getName()
    {
        return 'coupon';
    }
}
