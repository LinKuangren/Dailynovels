<?php

namespace App\Form;

use App\Entity\Novels;
use App\Entity\Tags;
use App\Entity\Categories;
use App\Repository\TagsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RechercheAvanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégories',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('Tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function(TagsRepository $tag){
                    return $tag->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'required' => false,
                'by_reference' => false
            ])
            ->add('TagsExcluded', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function(TagsRepository $tag){
                    return $tag->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'required' => false,
                'by_reference' => false,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'en cours',
                    'Terminé' => 'terminé',
                    'Abandonné' => 'abandonné',
                ],
            ])
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Trier par',
                'choices' => [
                    'Favoris' => 'favorites',
                    'Commentaires' => 'commentaires',
                    'Chapitres' => 'chapitres',
                ],
                'expanded' => false,
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    /*public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Novels::class,
        ]);
    }*/
}
