<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserNewpasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '********',],
                'label' => 'Mot de passe',
                'label_attr' => ['class' => 'form-label mt-4'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => true,
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '********'
                    ],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '********'
                    ],
                    'label' => 'Confirmation du nouveau mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Changer mon mot de passe'
            ]);
        ;
    }
}
