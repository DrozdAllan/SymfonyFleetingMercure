<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ResearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hair', ChoiceType::class, [
                'label' => 'Cheveux',
                'choices' => [
                    'Tout' => null,
                    'Courts' => 'Courts',
                    'Longs' => 'Longs',
                ],
                'required' => true
            ])
            ->add('tattoo', ChoiceType::class, [
                'label' => 'Tatouage',
                'choices' => [
                    'Tout' => null,
                    'Oui' => '1',
                    'Non' => '0',
                ],
                'required' => true
            ])
            ->add('smoke', ChoiceType::class, [
                'label' => 'Fume',
                'choices' => [
                    'Tout' => null,
                    'Oui' => '1',
                    'Non' => '0',
                ],
                'required' => true
            ])
            ->add('eyes', ChoiceType::class, [
                'label' => 'Yeux',
                'choices' => [
                    'Tout' => null,
                    'Bleu' => 'Blue',
                    'Vert' => 'Green',
                    'Brun' => 'Brown',
                ]
            ])
            ->add('nationality', ChoiceType::class, [
                'label' => 'Nationalité',
                'choices' => [
                    'Tout' => null,
                    'Française' => 'fr',
                    'Allemande' => 'de',
                    'Belge' => 'be',
                    'Espagnole' => 'es',
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Tout' => null,
                    'Français' => 'fr',
                    'Allemand' => 'de',
                    'Espagnol' => 'es',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
