<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Appointment;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/appointment/create', name: 'create_appointment', methods: ['POST'])]
    public function createAppointment(Request $request, EntityManagerInterface $em, UserRepository $userRepository, ServiceRepository $serviceRepository): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $date = $request->get('date');
            $prestationId = $request->get('prestation');
            $userId = $request->get('userId');

            $user = $userRepository->find($userId);
            if (!$user) {
                return new JsonResponse(['success' => false, 'message' => 'Utilisateur non trouvé'], 400);
            }

            $service = $serviceRepository->find($prestationId);
            if (!$service) {
                return new JsonResponse(['success' => false, 'message' => 'Service non trouvé'], 400);
            }

            $appointment = new Appointment();
            $appointment->setDate(new \DateTime($date));
            $appointment->setService($service);
            $appointment->setUser($user);
            $appointment->setPrice(0);
            $appointment->setDetail('Rendez-vous réservé');
            $appointment->setStatus(0);

            $em->persist($appointment);
            $em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Rendez-vous créé avec succès']);
        }

        return new JsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
    }
}
