<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create(); // Création de l'instance Faker
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un utilisateur admin
        $admin = new User();
        $admin->setFirstName('Mr');
        $admin->setLastName('Admin');
        $admin->setEmail('admin@mail.fr');
        $admin->setPhone($this->faker->phoneNumber()); // Numéro de téléphone aléatoire
        $admin->setRole('ROLE_ADMIN');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'mdp'));
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Création d'un utilisateur régulier
        $user = new User();
        $user->setFirstName('Mr');
        $user->setLastName('User');
        $user->setEmail('user@mail.fr');
        $user->setPhone($this->faker->phoneNumber()); // Numéro de téléphone aléatoire
        $user->setRole('ROLE_USER');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'mdp'));
        $manager->persist($user);
        $this->addReference('user_regular', $user);

        $testimonials = [
            "Un service exceptionnel et une équipe très professionnelle.",
            "Je suis très satisfait de ma coupe de cheveux. Merci !",
            "Des produits de qualité et un accueil chaleureux.",
            "Je recommande vivement ce salon de coiffure.",
            "Un grand merci pour votre professionnalisme.",
            "Je suis très content de ma nouvelle coupe de cheveux.",
            "Un salon de coiffure au top !",
            "Je suis ravi de ma coloration. Merci à toute l'équipe.",
            "Un service de qualité et des coiffeurs très compétents.",
            "C'est toujours un plaisir de venir dans ce salon.",
        ];

        // Création d'utilisateurs de test avec Faker
        for ($i = 0; $i < count($testimonials); $i++) {
            $user = new User();
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setEmail(strtolower($user->getFirstName()) . '.' . strtolower($user->getLastName()) . '@mail.fr');
            $user->setPhone($this->faker->phoneNumber());
            $user->setRole($this->faker->randomElement(['ROLE_USER', 'ROLE_ADMIN']));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'mdp'));

            $manager->persist($user);

            // Sélectionner un témoignage aléatoire
            $testimonial = $this->faker->randomElement($testimonials);

            // Création d'un avis pour cet utilisateur
            $review = new Review();
            $review->setUser($user); // Lier l'avis à l'utilisateur
            $review->setFeedback($testimonial); // Feedback aléatoire
            $review->setRating(rand(4, 5)); // Note aléatoire entre 4 et 5

            // Date aléatoire dans l'année passée
            $randomTimestamp = mt_rand(strtotime('-1 year'), time());
            $randomDate = (new \DateTime())->setTimestamp($randomTimestamp);
            $review->setDate($randomDate);

            $manager->persist($review);
        }

        // Sauvegarde des utilisateurs et des avis dans la base de données
        $manager->flush();
    }
}
