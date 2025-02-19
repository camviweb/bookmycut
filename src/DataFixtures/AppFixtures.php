<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        // Create admin user
        $admin = new User();
        $admin->setName('Admin');
        $admin->setSurname('User');
        $admin->setEmail('admin@example.com');
        $admin->setPhone('123456789');
        $admin->setRole('ROLE_ADMIN');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        // Create regular user
        $user = new User();
        $user->setName('Regular');
        $user->setSurname('User');
        $user->setEmail('user@example.com');
        $user->setPhone('987654321');
        $user->setRole('ROLE_USER');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        $manager->persist($user);

        // Save users to the database
        $manager->flush();
    }
}
