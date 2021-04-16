<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ModifyAnnouncerType extends AbstractType
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
