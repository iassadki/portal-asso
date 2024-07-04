<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationWebsiteType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        -> add('paiement', CheckboxType::class, [
            'label' => 'Activer les paiements',
            'required' => false,
            'mapped' => false,

        ])
        -> add('message', CheckboxType::class, [
            'label' => 'Activer les messages',
            'required' => false,
            'mapped' => false,
        ])
        -> add('evenement', CheckboxType::class, [
            'label' => 'Activer les événements',
            'required' => false,
            'mapped' => false,
        ])
        -> add('gallery', CheckboxType::class, [
            'label' => 'Activer la galerie',
            'required' => false,
            'mapped' => false,
        ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
