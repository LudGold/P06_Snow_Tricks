<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditFigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'attr' => ['class' => 'form-control']
        ])
        ->add('description', TextareaType::class, [
            'attr' => ['class' => 'form-control']
        ])
        ->add('createdAt', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control']
        ])
        ->add('slug', TextType::class, [
            'attr' => ['class' => 'form-control']
        ])
        ->add('author', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'id',
            'attr' => ['class' => 'form-control']
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'id',
            'attr' => ['class' => 'form-control']
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
        ]);
    }
}
