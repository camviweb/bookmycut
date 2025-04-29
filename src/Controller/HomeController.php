<?php

namespace App\Controller;

use App\Service\HomeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HomeService $homeService): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $homeService->getServices(),
            'avis' => $homeService->getAvis(),
        ]);
    }
}
