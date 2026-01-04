<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from a CSV file',
)]
class ImportProductsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] private string $projectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'The CSV filename in public folder', 'products.csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');
        $filePath = $this->projectDir . '/public/' . $filename;

        if (!file_exists($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle); // Skip header or use it to map
            // Assuming header is name, description, price

            $count = 0;
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) < 3) continue;

                $product = new Product();
                $product->setName($data[0]);
                $product->setDescription($data[1]);
                $product->setPrice((float) $data[2]);
                $product->setType('physical'); // Default

                $this->entityManager->persist($product);
                $count++;
            }
            fclose($handle);
            $this->entityManager->flush();

            $io->success(sprintf('%d products imported successfully.', $count));
            return Command::SUCCESS;
        }

        $io->error('Unable to open file.');
        return Command::FAILURE;
    }
}
