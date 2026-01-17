<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Produit Physique' => 'physical',
                    'Produit Numérique' => 'digital',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type de produit',
                'label_attr' => ['class' => 'radio-inline mr-4'],
                'row_attr' => ['class' => 'gap-4'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Product::class, // or DTO
            'validation_groups' => ['Default', 'step_type'],
        ]);
    }
}
