<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


 
class ConfigurationUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input-container',
                    'placeholder' => 'Email'
                ]
            ])
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'input-container',
                    'placeholder' => 'Ancien mot de passe'
                ]
                ])
            ->add('newPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'input-container',
                    'placeholder' => 'Nouveau mot de passe'
                    ]
                ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'input-container',
                    'placeholder' => 'Confirmer le mot de passe'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}