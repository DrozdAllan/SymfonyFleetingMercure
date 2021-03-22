<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
