<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('last_name', null,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Nom'
                    ]
                ])
            ->add('first_name', null,
                [
                    'label' => 'Prénom(s)',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Prénom(s)'
                    ]
                ])
            ->add('address', null,
                [
                    'label' => 'Adresse',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Adresse'
                    ]
                ])
            ->add('phone', null,
                [
                    'label' => 'Téléphone',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'numéro téléphone'
                    ]
                ])
            ->add('username', null,
                [
                    'label' => 'Identifiant',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Identifiant'
                    ]
                ])
            ->add('email', EmailType::class,
                [
                    'label' => 'Adresse email',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Adresse email'
                    ]
                ])
            ->add('password', PasswordType::class, 
                [
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Mot de passe'
                    ]
                ])
            ->add('plainPassword', PasswordType::class, 
                [
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'Confirmation mot de passe '
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['id' => 'form-registration']
        ]);
    }
}
