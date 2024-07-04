<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom de l\'événement',
                'attr' => [
                    'id' => 'event-name'
                ]
            ])
            ->add('description', null, [
                'label' => 'Description de l\'événement',
                'attr' => [
                    'id' => 'event-description'
                ]
            ])
            ->add('lieu', null, [
                'label' => 'Lieu de l\'événement',
                'attr' => [
                    'id' => 'event-location'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'id' => 'event-date'
                ]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'id' => 'event-date'
                ]
            ])  
            ->add('image', FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'id' => 'event-image', 

                ],
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
