<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnouncerRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'placeholder' => 'Entrez un nom d\'utilisateur'
                ]
            ])
            ->add('hair', ChoiceType::class, [
                'label' => 'Cheveux',
                'placeholder' => 'Choisissez une longueur de cheveux',
                'choices' => [
                    'Courts' => 'Courts',
                    'Longs' => 'Longs',
                ],
            ])
            ->add('tattoo', ChoiceType::class, [
                'label' => 'Tatouage',
                'placeholder' => 'Avez vous des tatouages',
                'choices' => [
                    'Oui' => '1',
                    'Non' => '0',
                ],
            ])
            ->add('smoke', ChoiceType::class, [
                'label' => 'Fume',
                'placeholder' => 'Vous arrive-t-il de fumer',
                'choices' => [
                    'Oui' => '1',
                    'Non' => '0',
                ],
            ])
            ->add('shortdescription', TextareaType::class, [
                'label' => 'Veuillez vous décrire en quelques mots',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Je reconnais avoir lu les conditions d\'utilisation et les accepte',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Entrez un mot de passe'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
