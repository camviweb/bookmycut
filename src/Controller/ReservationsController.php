<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationsController extends AbstractController
{
    #[Route('/reservations', name: 'app_reservations')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $prestationsHommes = $serviceRepository->findBy(['category' => 'hommes']);
        $prestationsFemmes = $serviceRepository->findBy(['category' => 'femmes']);

        return $this->render('reservations/index.html.twig', [
            'prestationsHommes' => $prestationsHommes,
            'prestationsFemmes' => $prestationsFemmes,
        ]);
    }
}
