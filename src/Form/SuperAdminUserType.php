<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuperAdminUserType extends AbstractType
{
 
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
->add('roles', ChoiceType::class, [
    'choices' => [
        'Admin' => 'ROLE_ADMIN',
        'Proprietaire' => 'ROLE_PROPRIETAIRE',
        'User' => 'ROLE_USER',
    ],
    'multiple' => true,
    'expanded' => false, // Par défaut, c'est false donc vous pouvez même omettre cette ligne
])
            ->add('password', PasswordType::class)
            ->add('nom')
            ->add('prenom')

            ->add('asso', EntityType::class, [
                'class' => Association::class,
                'choice_label' => 'id',
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
