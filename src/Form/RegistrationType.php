<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [ // Ajoutez le champ nom utilisateur
                'label' => 'Nom', // Étiquette du champ
                'required' => true, // Rend le champ obligatoire
                'constraints' => [ // Ajoutez éventuellement des contraintes de validation
                    new NotBlank([
                        'message' => 'Entrez votre nom',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom doit faire au minimum {{ limit }} characters',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [ // Ajoutez le champ prénom utilisateur
                'label' => 'Prénom', // Étiquette du champ
                'required' => true, // Rend le champ obligatoire
                'constraints' => [ // Ajoutez éventuellement des contraintes de validation
                    new NotBlank([
                        'message' => 'Entrez votre prénom',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre prénom doit faire au minimum {{ limit }} characters',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new IsTrue([
                        'message' => 'Vous devez approuver les conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => 'email',
                    'message' => 'Cette adresse e-mail est déjà utilisée.',
                ]),
            ],
        ]);
    }
}
