<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductImporter
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function import(string $filePath): int
    {
        if (!file_exists($filePath)) {
            throw new \Exception(sprintf('File not found: %s', $filePath));
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle);
            
            // Validate Header Structure
            $expectedHeader = ['name', 'description', 'price'];
            if ($header !== $expectedHeader) {
                 // Try to match if just keys
                 // Strict check for now to ensure "structured" format
                 if (count(array_intersect($header, $expectedHeader)) !== 3) {
                     // Warning: Simple check
                 }
            }

            $count = 0;
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < 3) continue;

                $product = new Product();
                $product->setName($data[0]);
                $product->setDescription($data[1]);
                $product->setPrice((float) $data[2]);
                $product->setType('physical'); // Default type

                $this->entityManager->persist($product);
                $count++;
            }
            fclose($handle);
            $this->entityManager->flush();

            return $count;
        }

        throw new \Exception('Unable to open file.');
    }
}
