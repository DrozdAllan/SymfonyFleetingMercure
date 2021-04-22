<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminAnnouncersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username', TextType::class, [
            'label' => 'Nom d\'utilisateur'
        ])
        ->add('mail', EmailType::class, [
            'label' => 'Mail',
            'attr' => [
                'disabled' => true
            ]
        ])
        ->add('hair', ChoiceType::class, [
            'label' => 'Cheveux',
            'choices' => [
                'Courts' => 'Courts',
                'Longs' => 'Longs',
            ],
            'attr' => [
                'disabled' => true
            ]
        ])
        ->add('tattoo', ChoiceType::class, [
            'label' => 'Tatouage',
            'choices' => [
                'Oui' => '1',
                'Non' => '0',
            ],
            'attr' => [
                'disabled' => true
            ]
        ])
        ->add('smoke', ChoiceType::class, [
            'label' => 'Fume',
            'choices' => [
                'Oui' => '1',
                'Non' => '0',
            ],
            'attr' => [
                'disabled' => true
            ]
        ])
        ->add('shortdescription', TextareaType::class, [
            'label' => 'Description de l\'annonceur',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
