<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom de la figure',
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control']

            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Choisissez une catégorie',
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count(min: 1, minMessage: 'veuillez ajouter une image', groups: ['create', 'edit']),
                    new Valid(),

                ],
            ])

            ->add('videos', CollectionType::class, [
                'label' => false,
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Count(min: 1, minMessage: 'Vous devez ajouter au moins une vidéo.', groups: ['create', 'edit'])
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
            'validation_groups' => [],
        ]);
    }
}
