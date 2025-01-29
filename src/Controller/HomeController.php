<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Données fictives pour les services
        $services = [
            [
                'name' => 'Coupe de cheveux',
                'description' => 'Une coupe de cheveux stylée pour vous sublimer.',
                'image' => '/images/services/coupe.jpg',
                'slug' => 'coupe-de-cheveux',
            ],
            [
                'name' => 'Coloration',
                'description' => 'Des couleurs vibrantes pour vos cheveux.',
                'image' => '/images/services/coloration.jpg',
                'slug' => 'coloration',
            ],
            [
                'name' => 'Soin capillaire',
                'description' => 'Des soins pour des cheveux en pleine santé.',
                'image' => '/images/services/soin.jpg',
                'slug' => 'soin-capillaire',
            ],
        ];

// Données fictives pour les témoignages
        $testimonials = [
            [
                'name' => 'Alice',
                'profile_image' => '/images/testimonials/alice.jpg',
                'feedback' => 'Un service exceptionnel et une équipe très professionnelle.',
                'rating' => 5,
                'date' => '10 décembre 2024',
            ],
            [
                'name' => 'Bob',
                'profile_image' => '/images/testimonials/bob.jpg',
                'feedback' => 'Je suis très satisfait de ma coupe de cheveux. Merci !',
                'rating' => 5,
                'date' => '5 janvier 2025',
            ],
            [
                'name' => 'Charlie',
                'profile_image' => '/images/testimonials/charlie.jpg',
                'feedback' => 'Des produits de qualité et un accueil chaleureux.',
                'rating' => 5,
                'date' => '20 décembre 2024',
            ],
            [
                'name' => 'David',
                'profile_image' => '/images/testimonials/david.jpg',
                'feedback' => 'Je recommande vivement ce salon de coiffure.',
                'rating' => 5,
                'date' => '15 janvier 2025',
            ],
            [
                'name' => 'Eve',
                'profile_image' => '/images/testimonials/eve.jpg',
                'feedback' => 'Un grand merci pour votre professionnalisme.',
                'rating' => 5,
                'date' => '25 décembre 2024',
            ],
            [
                'name' => 'Frank',
                'profile_image' => '/images/testimonials/frank.jpg',
                'feedback' => 'Je suis très content de ma nouvelle coupe de cheveux.',
                'rating' => 5,
                'date' => '30 décembre 2024',
            ],
            [
                'name' => 'Grace',
                'profile_image' => '/images/testimonials/grace.jpg',
                'feedback' => 'Un salon de coiffure au top !',
                'rating' => 5,
                'date' => '1 janvier 2025',
            ],
        ];
        
        return $this->render('home.html.twig', [
            'services' => $services,
            'testimonials' => $testimonials,
        ]);
    }
}
