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
                    'label' => 'app.auth.last_name',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'app.auth.last_name'
                    ]
                ])
            ->add('first_name', null,
                [
                    'label' => 'app.auth.first_name',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'app.auth.first_name'
                    ]
                ])
            ->add('username', null,
                [
                    'label' => 'app.auth.username',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'app.auth.username'
                    ]
                ])
            ->add('email', EmailType::class,
                [
                    'label' => 'app.auth.email',
                    'attr' => [
                        'class'=>'form-control required-input',
                        'placeholder' => 'app.auth.email'
                    ]
                ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class'=>'form-control required-input',
                        'placeholder' => 'app.auth.password'
                    ]
                ],
                'first_options' => ['label' => 'app.auth.password',],
                'second_options' => ['label' => 'app.auth.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
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
