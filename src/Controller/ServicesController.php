<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {
        $coupesFemmes = [
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

        $coupesHommes = [
            [
                'nom' => 'cheveux courts',
                'description' => 'Une coupe courte pour un style tendance et facile à entretenir !',
                'image' => 'homme-courte.png',
                'duree' => '30 min',
                'prix' => '20 €',
                'etapes' => ['Shampoing', 'Coupe et mise en forme']
            ],
            [
                'nom' => 'cheveux longs',
                'description' => 'Une coupe sur-mesure pour des cheveux longs sublimes et en pleine santé !',
                'image' => 'homme-longue.png',
                'duree' => '30 min',
                'prix' => '30 €',
                'etapes' => ['Shampoing', 'Coupe et mise en forme']
            ],
            [
                'nom' => 'barbe',
                'description' => 'Taille et entretien de la barbe pour un look soigné et élégant !',
                'image' => 'homme-barbe.png',
                'duree' => '30 min',
                'prix' => '15 €',
                'etapes' => ['Nettoyage de la barbe', 'Taille et mise en forme']
            ]
        ];

        return $this->render('services/index.html.twig', [
            'coupes_femmes' => $coupesFemmes,
            'coupes_hommes' => $coupesHommes
        ]);
    }

    #[Route('/services/filtered/{category}', name: 'app_services_filtered')]
    public function filteredServices($category, ServiceRepository $serviceRepository): JsonResponse
    {
        // Récupère les services par catégorie
        $services = $serviceRepository->findBy(['category' => $category]);

        // Envoie la liste des services au format JSON
        $response = [];
        foreach ($services as $service) {
            $response[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
            ];
        }

        return new JsonResponse($response);
    }
}
