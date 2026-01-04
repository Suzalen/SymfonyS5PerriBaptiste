<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductLogisticsStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weight', NumberType::class, ['label' => 'Poids (kg)', 'required' => false])
            ->add('dimensions', TextType::class, ['label' => 'Dimensions (LxHxP)', 'required' => false])
            ->add('stock', IntegerType::class, ['label' => 'Stock initial']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Product::class,
            'validation_groups' => ['Default', 'step_logistics'],
        ]);
    }
}
