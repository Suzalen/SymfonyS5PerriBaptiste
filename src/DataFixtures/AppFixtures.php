<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
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

        // Clients
        for ($i = 1; $i <= 5; $i++) {
            $client = new \App\Entity\Client();
            $client->setFirstname('ClientFirst' . $i);
            $client->setLastname('ClientLast' . $i);
            $client->setEmail('client' . $i . '@test.com');
            $client->setPhoneNumber('010203040' . $i);
            $client->setAddress($i . ' Rue du Test');
            $manager->persist($client);
        }

        $manager->flush();
    }
}
