<?php

namespace App\Controller;

use App\Service\ReservationsService;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Security\AuthentificatorAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

final class ReservationsController extends AbstractController
{
    private ReservationsService $reservationsService;

    public function __construct(ReservationsService $reservationsService)
    {
        $this->reservationsService = $reservationsService;
    }

    #[Route('/reservations', name: 'app_reservations')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $horaires = [
            '9:00', '9:30', '10:00', '10:30', '11:00', '11:30', '12:00',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00',
            '16:30', '17:00', '17:30', '18:00'
        ];
        $prestations = [
            'hommes' => $serviceRepository->findBy(['category' => 'hommes']),
            'femmes' => $serviceRepository->findBy(['category' => 'femmes']),
        ];
        return $this->render('reservations/index.html.twig', [
            'prestations' => $prestations,
            'horaires' => $horaires,
        ]);
    }
    #[Route('/reservations/create', name: 'app_create_reservation', methods: ['POST'])]
    public function createAppointment(Request $request, EntityManagerInterface $em, 
    UserRepository $userRepository, 
    ServiceRepository $serviceRepository, 
    SessionInterface $session, 
    AppointmentRepository $appointmentRepository, 
    MailerInterface $mailer, 
    UserPasswordHasherInterface $passwordHasher, 
    UserAuthenticatorInterface $authenticator, 
    AuthentificatorAuthenticator $appAuthenticator): RedirectResponse
    {
        $date = $request->get('date');
        $horaire = $request->get('horaire');
        $prestationId = $request->get('prestation');
        $user = $this->getUser();
        if(!$user){
            $email = $request->get('email');
        }  
        else{
            $email = $user->getEmail();
        }

        
        // Appel du service pour créer un rendez-vous
        $route = $this->reservationsService->createAppointment(
            $request,
            $session,
            $date,
            $horaire,
            $prestationId,
            $email
        );

        return $this->redirectToRoute('app_reservations'); // Redirige vers la page de réservation
    }


    #[Route('/reservations/check-slots', name: 'app_check_slots', methods: ['GET'])]
    public function checkSlots(Request $request, AppointmentRepository $appointmentRepository): Response
    {
        $date = $request->get('date');

        $startOfDay = new \DateTime($date . ' 00:00:00');
        $endOfDay = new \DateTime($date . ' 23:59:59');

        $appointments = $appointmentRepository->findAppointmentsBetweenDates($startOfDay, $endOfDay);

        $reservedSlots = [];
        foreach ($appointments as $appointment) {
            $reservedSlots[] = $appointment->getDate()->format('H:i');
        }

        return $this->json(['reservedSlots' => $reservedSlots]);
    }

    #[Route('/mes-reservations', name: 'app_mes_reservations')]
    #[IsGranted('ROLE_USER')]
    public function myReservations(AppointmentRepository $appointmentRepository): Response
    {
        $reservations = $appointmentRepository->findBy(['user' => $this->getUser()], ['date' => 'DESC']);

        return $this->render('reservations/my_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

}
