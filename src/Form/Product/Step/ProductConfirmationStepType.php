<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductConfirmationStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirmed', CheckboxType::class, [
                'label' => 'Je confirme la création de ce produit coûteux.',
                'mapped' => false,
                'constraints' => [new IsTrue(['message' => 'Vous devez confirmer.'])],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Product::class,
        ]);
    }
}
