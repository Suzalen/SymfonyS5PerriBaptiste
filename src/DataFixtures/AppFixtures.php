<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Faker\Factory;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('System');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // Manager
        $managerUser = new User();
        $managerUser->setEmail('manager@example.com');
        $managerUser->setFirstname('Manager');
        $managerUser->setLastname('System');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword($this->userPasswordHasher->hashPassword($managerUser, 'password'));
        $manager->persist($managerUser);

        // User
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // Create 10 Clients
        for ($i = 0; $i < 10; $i++) {
            $client = new Client();
            $client->setFirstname($faker->firstName);
            $client->setLastname($faker->lastName);
            $client->setEmail($faker->email);
            $client->setCompany($faker->company);
            $client->setPhoneNumber($faker->phoneNumber);
            $client->setAddress($faker->address);
            $client->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $manager->persist($client);
        }

        // Create 10 Products
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->word . ' ' . $faker->word);
            $product->setDescription($faker->sentence(10));
            $product->setPrice($faker->randomFloat(2, 10, 500));
            $product->setType($i % 2 === 0 ? 'physical' : 'digital');
            
            if ($product->getType() === 'physical') {
                $product->setWeight($faker->randomFloat(2, 0.5, 50));
                $product->setStock($faker->numberBetween(0, 100));
                $product->setDimensions('10x20x30');
            } else {
                $product->setLicenseDetails('Standard License ' . $faker->year);
            }
            
            $manager->persist($product);
        }

        $manager->flush();
    }
}
