<?php

namespace App\Form;

use App\Entity\Novels;
use App\Entity\Tags;
use App\Entity\Categories;
use App\Entity\Traducteurs;
use App\Repository\TagsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class NovelsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Super Detective in the Fictional World',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'allow_delete' => false,
                'required' => false,
                'download_uri' => false,
                'image_uri' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                ],
            ])
            ->add('traducteurs', EntityType::class, [
                'class' => Traducteurs::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ])
            ->add('author', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '冰原三雅',
                ],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function(TagsRepository $er){
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'required' => false,
                'by_reference' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'en cours',
                    'Terminé' => 'terminé',
                    'Abandonné' => 'abandonné',
                ],
            ])
            ->add('visibilitie', CheckboxType::class, [
                'required' => false,
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Novels::class,
        ]);
    }
}
