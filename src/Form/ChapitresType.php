<?php

namespace App\Form;

use App\Entity\Chapitres;
use App\Entity\Novels;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ChapitresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'ch 122',
                ],
            ])
            ->add('link', TextType::class, [
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/watch?v=gkTb9GP9lVI&ab_channel=JwHDify',
                ],
            ])
            ->add('novels', EntityType::class, [
                'class' => Novels::class,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapitres::class,
        ]);
    }
}
