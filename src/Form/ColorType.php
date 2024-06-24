<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Association;
// Ensure to use the correct namespace for the ColorType you intend to use
use Symfony\Component\Form\Extension\Core\Type\ColorType as SymfonyColorType;

class ColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('couleurPrimaire', SymfonyColorType::class, [
                'label' => 'Couleur Primaire',
                'required' => false,
                'mapped' => false,
            ])
            ->add('couleurSecondaire', SymfonyColorType::class, [
                'label' => 'Couleur Secondaire',
                'required' => false,
                'mapped' => false,
            ])
            ->add('couleurTertiaire', SymfonyColorType::class, [
                'label' => 'Couleur Tertiaire',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}