<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('last_name', null, [
                    'label' => 'Nom',
                    'required' => true
                ])
            ->add('first_name', null, [
                    'label' => 'Prénom(s)',
                    'required' => true
                ])
            ->add('address', null, [
                    'label' => 'Adresse',
                    'required' => true
                ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true
            ])
            ->add('username', null, [
                    'label' => 'Identifiant',
                    'required' => true
                ])
            ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => true
                ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe'
                ],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['id' => 'form-registration']
        ]);
    }
}
