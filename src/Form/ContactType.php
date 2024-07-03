<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class,
            ['mapped' => false])
            ->add('Prenom', TextType::class,
            ['mapped' => false])
            ->add('email', EmailType::class,
            ['mapped' => false])
            ->add('objet', TextType::class)
            ->add('message', TextareaType::class)
            ->add('file', FileType::class, 
            ['label' => 'Ajouter une pièce jointe', 'mapped' => false, 'required' => false])
            // ->add('checkbox', CheckboxType::class, ['label' => 'Accepter les conditions générales d\'utilisation', 'mapped' => false, 'constraints' => new IsTrue()])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }

}