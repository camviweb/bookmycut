<?php

namespace App\Controller;

use App\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(Request $request, RegistrationService $registrationService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $result = $registrationService->handleRegistration($request);

        if ($result instanceof Response) {
            return $result; // Redirection après inscription réussie
        }

        return $this->render('registration/registration.html.twig', $result);
    }
}
