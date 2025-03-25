<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Product;
use App\Entity\Appointment;
use App\Entity\ProductUsage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Fonction pour nettoyer les caractères non ASCII
        function cleanString($str) {
            // On remplace les caractères spéciaux par des lettres ASCII et on retire les symboles indésirables
            return preg_replace('/[^a-zA-Z0-9.-]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str));
        }

        // Création de services
        $services = [];
        $serviceData = [
            ['name' => 'Coupe Homme', 'category' => 'hommes', 'photo' => 'homme-courte.png'],
            ['name' => 'Barbe', 'category' => 'hommes', 'photo' => 'homme-barbe.png'],
            ['name' => 'Coupe Femme', 'category' => 'femmes', 'photo' => 'femme-courte.png'],
            ['name' => 'Brushing', 'category' => 'femmes', 'photo' => 'femme-longue.png'],
            ['name' => 'Coloration', 'category' => 'femmes', 'photo' => 'femme-longue.png'],
            ['name' => 'Permanente', 'category' => 'femmes', 'photo' => 'femme-permanente.png'],
            ['name' => 'Lissage', 'category' => 'femmes', 'photo' => 'femme-longue.png'],
            ['name' => 'Mèches', 'category' => 'femmes', 'photo' => 'femme-courte.png'],
            ['name' => 'Soin capillaire', 'category' => 'femmes', 'photo' => 'femme-longue.png'],
            ['name' => 'Massage du cuir chevelu', 'category' => 'hommes', 'photo' => 'homme-courte.png'],
            ['name' => 'Rasage à l’ancienne', 'category' => 'hommes', 'photo' => 'homme-barbe.png'],
            ['name' => 'Taille de la moustache', 'category' => 'hommes', 'photo' => 'homme-barbe.png'],
        ];

        foreach ($serviceData as $data) {
            $service = new Service();
            $service
                ->setName($data['name'])
                ->setDescription(implode(' ', $this->faker->words(10)))
                ->setPrice(mt_rand(15, 45))
                ->setDuration(mt_rand(30, 90))
                ->setCategory($data['category'])
                ->setPhoto($data['photo']);

            $manager->persist($service);
            $services[] = $service;
        }

        // Création de produits
        $products = [];
        $productNames = ['Shampooing', 'Gel coiffant', 'Colorant', 'Masque capillaire', 'Après-shampooing', 'Sérum capillaire'];
        $brands = ['L\'Oréal', 'Kérastase', 'Schwarzkopf', 'Wella', 'René Furterer', 'Dop', 'Madara', 'Garnier', 'Head & Shoulders'];

        foreach ($productNames as $name) {
            $product = new Product();
            $product
                ->setType($name)
                ->setBrand($brands[array_rand($brands)])
                ->setDescription(implode(' ', $this->faker->words(10)))
                ->setQuantity(mt_rand(5, 30))
                ->setUnitPrice(mt_rand(5, 30));
            $manager->persist($product);
            $products[] = $product;
        }

        // Création de l'utilisateur (admin)
        $admin = new User();
        $admin
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setEmail('admin@bookmycut.fr')
            ->setPhone('+33 6 12 34 56 78')
            ->setRole('ROLE_ADMIN')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'mdp'));
        $manager->persist($admin);

        // Création des utilisateurs (clients)
        $users = [];
        for ($i = 0; $i < 25; $i++) {  // On crée 25 utilisateurs
            $user = new User();
            $firstName = $this->faker->firstName();
            $lastName = $this->faker->lastName();
            $email = strtolower(cleanString($firstName)) . '.' . strtolower(cleanString($lastName)) . '@mail.fr';

            $user
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPhone('+33 6 ' . $this->faker->numerify('## ## ## ##'))
                ->setRole('ROLE_USER')
                ->setPassword($this->passwordHasher->hashPassword($user, 'mdp'));
            $manager->persist($user);
            $users[] = $user;  // On les ajoute au tableau des utilisateurs
        }

        // Définir les horaires
        $horaires = [
            '9:00', '9:30', '10:00', '10:30', '11:00',
            '11:30', '12:00', '13:00', '13:30', '14:00',
            '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00'
        ];

        // Suppression des créneaux après 14h pour les samedis
        $samediHoraires = array_filter($horaires, function ($horaire) {
            return strtotime($horaire) <= strtotime('14:00');
        });

        // Création de 45 rendez-vous (15 pour chaque mois)
        $dates = [
            'lastMonth' => ['start' => 'first day of last month', 'end' => 'last day of last month'],  // Mois dernier
            'currentMonth' => ['start' => 'first day of this month', 'end' => 'last day of this month'],  // Mois actuel
            'nextMonth' => ['start' => 'first day of next month', 'end' => 'last day of next month'],  // Mois prochain
        ];

        foreach ($dates as $key => $range) {
            for ($i = 0; $i < 15; $i++) { // 15 rendez-vous par mois
                $appointment = new Appointment();
                $service = $services[array_rand($services)]; // Choisir un service aléatoire

                // Choisir une date aléatoire dans le mois spécifique
                $appointmentDate = $this->faker->dateTimeBetween($range['start'], $range['end']);

                // Exclure les dimanches
                if ($appointmentDate->format('l') == 'Sunday') {
                    continue;  // Passer à l'itération suivante sans créer de rendez-vous
                }

                // Si c'est un samedi, on limite l'heure à 14h
                if ($appointmentDate->format('l') == 'Saturday') {
                    $horaire = $this->faker->randomElement($samediHoraires);
                } else {
                    $horaire = $this->faker->randomElement($horaires);
                }

                $appointment->setDate(new \DateTime($appointmentDate->format('Y-m-d') . ' ' . $horaire));
                $appointment->setPrice($service->getPrice())
                    ->setDetail(implode(' ', $this->faker->words(10)))
                    ->setStatus($this->faker->randomElement([0, 1, 2]))
                    ->setService($service);

                // Associer un ou plusieurs utilisateurs (clients)
                $numClients = mt_rand(1, 2);
                for ($j = 0; $j < $numClients; $j++) {
                    $client = $users[array_rand($users)];
                    $appointment->setUser($client);  // Ajouter le client au rendez-vous
                }

                $manager->persist($appointment);

                // Ajouter l'utilisation des produits
                for ($j = 0; $j < mt_rand(1, 3); $j++) {  // 1 à 3 produits par rendez-vous
                    $usage = new ProductUsage();
                    $usage
                        ->setProduct($products[array_rand($products)])  // Choisir un produit aléatoire
                        ->setQuantityUsed(mt_rand(1, 5));  // Quantité utilisée entre 1 et 5
                    $appointment->addProductusage($usage);
                    $manager->persist($usage);
                }
            }
        }

        // Création des avis
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

        for ($i = 0; $i < count($testimonials); $i++) {
            $user = new User();
            $firstName = $this->faker->firstName();
            $lastName = $this->faker->lastName();
            $email = strtolower(cleanString($firstName)) . '.' . strtolower(cleanString($lastName)) . '@mail.fr';

            $user
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPhone('+33 6 ' . $this->faker->numerify('## ## ## ##'))
                ->setRole('ROLE_USER')
                ->setPassword($this->passwordHasher->hashPassword($user, 'mdp'));

            $manager->persist($user);

            // Création d'un avis pour cet utilisateur
            $review = new Review();
            $review
                ->setUser($user)
                ->setFeedback($this->faker->randomElement($testimonials))
                ->setRating(rand(4, 5));

            // Date aléatoire dans l'année passée
            $randomTimestamp = mt_rand(strtotime('-1 year'), time());
            $randomDate = (new \DateTime())->setTimestamp($randomTimestamp);
            $review->setDate($randomDate);

            $manager->persist($review);
        }


        // Sauvegarde des données
        $manager->flush();
    }

    }