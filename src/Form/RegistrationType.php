<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '180',
                    'placeholder' => 'azerty@gmail.com'
                ],
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 180])
                ],
                'required' => true
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'pseudo',
                    'minlength' => '4',
                    'maxlength' => '50',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 4, 'max' => 50])
                ],
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '********',
                        'minlength' => '8',
                        'maxlength' => '64',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 8, 'max' => 64])
                    ],
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '********',
                        'minlength' => '8',
                        'maxlength' => '64',
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 8, 'max' => 64])
                    ],
                    'label' => 'Confirmation du mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
            ])
            ->add('rgpd', CheckboxType::class, [
                'label' => 'En cochant cette case vous accepter la RGPD du site.',
                'required' => true,
                'mapped' => false,
            ])
            ->add('Valider', SubmitType::class, [
                'label' => 'S\'inscrire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
