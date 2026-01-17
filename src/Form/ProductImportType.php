<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csv_file', FileType::class, [
                'label' => 'Fichier CSV (name, description, price)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain', // Windows CSV often text/plain
                            'application/vnd.ms-excel',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier CSV valide',
                    ])
                ],
                'attr' => [
                    'class' => 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none'
                ]
            ])
            ->add('import', SubmitType::class, [
                'label' => 'Importer les produits',
                'attr' => ['class' => 'bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
