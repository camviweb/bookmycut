<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $coupesFemmes = $serviceRepository->findBy(['category' => 'femmes']);
        $coupesHommes = $serviceRepository->findBy(['category' => 'hommes']);

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