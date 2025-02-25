<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
