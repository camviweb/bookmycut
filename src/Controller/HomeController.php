<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ReviewRepository $reviewRepository): Response
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

        $avis = $reviewRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'services' => $services,
            'avis' => $avis,
        ]);
    }
}
