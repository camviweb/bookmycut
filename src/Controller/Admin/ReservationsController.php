<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Appointment;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class ReservationsController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    #[Route('/', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        $reservations = $this->appointmentRepository->findAll();

        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'title' => $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName(),
                'client' => $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName(),
                'service' => $reservation->getService()->getName(),
                'description' => $reservation->getDetail(),
                'price' => $reservation->getPrice(),
                'status' => $reservation->getStatus(),
                'duration' => $reservation->getService()->getDuration(),
                'start' => $reservation->getDate()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getDate()->modify('+' . $reservation->getService()->getDuration() . ' minutes')->format('Y-m-d\TH:i:s'),
            ];
        }

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $events,
        ]);
    }
    #[Route('/new', name: 'app_admin_reservations_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
/*
       $horaires = [
            '9:00', '9:30', '10:00', '10:30', '11:00', '11:30', '12:00',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00',
            '16:30', '17:00', '17:30', '18:00'
        ];

        $prestations = [
            'hommes' => $serviceRepository->findBy(['category' => 'hommes']),
            'femmes' => $serviceRepository->findBy(['category' => 'femmes']),
        ];
*/

        $appointment = new Appointment();
        $appointment->setStatus(0);

        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nomPrenom = $form->get('newUserFullName')->getData();
            $selectedService = $appointment->getService();

            // Si un service est sélectionné, récupérer les infos
            if ($selectedService) {
                $appointment->setPrice($selectedService->getPrice());
                $appointment->setDetail($selectedService->getDescription());
            }

            // Création d'un utilisateur si aucun n'est sélectionné
            if (!$appointment->getUser() && $nomPrenom) {
                $user = new User();
                $user->setFirstName(explode(' ', $nomPrenom)[0]);
                $user->setLastName(explode(' ', $nomPrenom)[1]);
                $user->setEmail(strtolower(str_replace(' ', '.', $nomPrenom)) . '@exemple.com');
                $user->setPhone('');
                $user->setRole('ROLE_USER');

                // Génération d'un mot de passe temporaire
                $password = bin2hex(random_bytes(4));
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('info', "Un nouveau client a été créé : {$user->getFirstName()} {$user->getLastName()}.");

                // Associer l'utilisateur au rendez-vous
                $appointment->setUser($user);
            }

            $entityManager->persist($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Le rendez-vous a été créé avec succès pour ' . $appointment->getUser()->getFirstName() . ' ' . $appointment->getUser()->getLastName() . ' le ' . $appointment->getDate()->format('d/m/Y') . ' à ' . $appointment->getDate()->format('H:i') . '.');
            return $this->redirectToRoute('app_admin_reservations');
        }

        return $this->render('admin/reservations_new.html.twig', [
            'form' => $form->createView(),
//            'horaires' => $horaires,
//            'prestations' => $prestations,
        ]);
    }


    #[Route('/api/service/{id}', name: 'api_service_details', methods: ['GET'])]
    public function getServiceDetails(Service $service): JsonResponse
    {
        return $this->json([
            'price' => $service->getPrice(),
            'description' => $service->getDescription(),
        ]);
    }

}
