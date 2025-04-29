<?php

namespace App\Service\Admin;

use App\Entity\Service;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Appointment;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;


class ReservationsService
{
    private AppointmentRepository $appointmentRepository;
    private ReservationsService $reservationsService;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private FormFactoryInterface $formFactory;


    public function __construct(AppointmentRepository $appointmentRepository,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
    FormFactoryInterface $formFactory)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->formFactory = $formFactory;
    }

    public function getAllReservations(): array
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

        return $events;
    }
    
    public function createAppointment(Request $request, SessionInterface $session):?\Symfony\Component\Form\FormInterface
    {
        $appointment = new Appointment();
        $appointment->setStatus(0);

        $form = $this->formFactory->create(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nomPrenom = $form->get('newUserFullName')->getData();
            $selectedService = $appointment->getService();

            if ($selectedService) {
                $appointment->setPrice($selectedService->getPrice());
                $appointment->setDetail($selectedService->getDescription());
            }

            if (!$appointment->getUser() && $nomPrenom) {
                $user = new User();
                $user->setFirstName(explode(' ', $nomPrenom)[0]);
                $user->setLastName(explode(' ', $nomPrenom)[1]);
                $user->setEmail(strtolower(str_replace(' ', '.', $nomPrenom)) . '@exemple.com');
                $user->setPhone('');
                $user->setRole('ROLE_USER');

                $password = bin2hex(random_bytes(4));
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $session->getFlashBag()->add('info', "Un nouveau client a été créé : {$user->getFirstName()} {$user->getLastName()}.");

                // Associer l'utilisateur au rendez-vous
                $appointment->setUser($user);
            }

            $this->entityManager->persist($appointment);
            $this->entityManager->flush();

            $session->getFlashBag()->add('success', 'Le rendez-vous a été créé avec succès pour ' . $appointment->getUser()->getFirstName() . ' ' . $appointment->getUser()->getLastName() . ' le ' . $appointment->getDate()->format('d/m/Y') . ' à ' . $appointment->getDate()->format('H:i') . '.');

            return $form; // Indique que la création est terminée
        }
        return $form;
    }
}
