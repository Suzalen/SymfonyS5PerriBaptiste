<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-client',
    description: 'Create a new client via CLI',
)]
class CreateClientCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $firstname = $io->ask('Firstname');
        $lastname = $io->ask('Lastname');
        $email = $io->ask('Email');
        $phone = $io->ask('Phone Number');
        $address = $io->ask('Address');

        $client = new Client();
        $client->setFirstname($firstname);
        $client->setLastname($lastname);
        $client->setEmail($email);
        $client->setPhoneNumber($phone);
        $client->setAddress($address);
        
        // Basic validation check (simulated, ideally use Validator service)
        if (empty($firstname) || empty($lastname) || empty($email)) {
             $io->error('Firstname, Lastname, and Email are required.');
             return Command::FAILURE;
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success('Client created successfully.');

        return Command::SUCCESS;
    }
}
