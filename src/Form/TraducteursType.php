<?php

namespace App\Form;

use App\Entity\Traducteurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TraducteursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Novels de Glace',
                ],
            ])
            ->add('link', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                ],
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Traducteurs::class,
        ]);
    }
}
