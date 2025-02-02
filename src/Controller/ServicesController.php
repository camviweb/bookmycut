<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {
        $coupes = [
            [
                'nom' => 'cheveux courts',
                'description' => 'Sublimez votre style avec une coupe courte tendance et personnalisée !',
                'image' => 'femme-courte.png',
                'duree' => '30 min',
                'prix' => '30 €',
                'etapes' => ['Shampoing', 'Coupe et mise en forme']
            ],
            [
                'nom' => 'cheveux longs',
                'description' => 'Une coupe sur-mesure pour des cheveux longs sublimes et en pleine santé !',
                'image' => 'femme-longue.png',
                'duree' => '30 min',
                'prix' => '50 €',
                'etapes' => ['Shampoing', 'Coupe et mise en forme']
            ],
            [
                'nom' => 'permanente',
                'description' => 'Des boucles parfaites et du volume longue durée !',
                'image' => 'femme-permanente.png',
                'duree' => '1h 30min',
                'prix' => '80 €',
                'etapes' => ['Préparation des cheveux', 'Application du produit fixateur', 'Neutralisation et rinçage']
            ]
        ];

        return $this->render('services/index.html.twig', [
            'controller_name' => 'ServicesController',
            'coupes' => $coupes
        ]);
    }
}
