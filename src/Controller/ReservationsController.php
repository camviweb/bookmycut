<?php

namespace App\Controller;

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
    public function createAppointment(Request $request, EntityManagerInterface $em, UserRepository $userRepository, ServiceRepository $serviceRepository, SessionInterface $session, AppointmentRepository $appointmentRepository, MailerInterface $mailer, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $authenticator, AuthentificatorAuthenticator $appAuthenticator): RedirectResponse
    {
        $date = $request->get('date');
        $horaire = $request->get('horaire');
        $prestationId = $request->get('prestation');
        $email = $request->get('email');

        $service = $serviceRepository->find($prestationId);
        if (!$service) {
            $session->getFlashBag()->add('danger', 'Prestation introuvable.');
            return $this->redirectToRoute('app_reservations');
        }

        $dateTime = new \DateTime("$date $horaire");

        $existingAppointment = $appointmentRepository->findOneBy(['date' => $dateTime]);
        if ($existingAppointment) {
            $session->getFlashBag()->add('danger', 'Un rendez-vous existe déjà pour cette date et heure.');
            return $this->redirectToRoute('app_reservations');
        }

        $user = $this->getUser();
        if (!$user) {
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user) {
                // L'email existe déjà
                $session->getFlashBag()->add('warning', 'Un compte avec cet email existe déjà. Connectez-vous ici : <a href="' . $this->generateUrl('app_login') . '">Connexion</a>');
                return $this->redirectToRoute('app_reservations');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setLastName($request->get('lastname'));
            $user->setFirstname($request->get('firstname'));
            $user->setPhone($request->get('phone'));
            $user->setRole('ROLE_USER');

            $password = bin2hex(random_bytes(4)); // Génère un mot de passe temporaire
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $em->persist($user);

            try {
                $confirmationEmail = (new Email())
                    ->from('no-reply@bookmycut.com')
                    ->to($user->getEmail())
                    ->subject('Votre compte a été créé')
                    ->html("
                                        <h2>Bienvenue sur notre plateforme !</h2>
                                        <p>Votre compte a été créé automatiquement lors de votre réservation.</p>
                                        <p>Voici votre mot de passe temporaire : <strong>$password</strong></p>
                                        <p>Veuillez le modifier dès votre première connexion.</p>
                                    ");
                $mailer->send($confirmationEmail);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('warning', 'Votre compte a été créé, mais nous n\'avons pas pu envoyer l\'email de confirmation.');
            }

            // Auto-login après la création du compte
            $authenticator->authenticateUser(
                $user,
                $appAuthenticator,
                $request
            );
        }

        $appointment = new Appointment();
        $appointment->setDate($dateTime);
        $appointment->setService($service);
        $appointment->setUser($user);
        $appointment->setPrice($service->getPrice());
        $appointment->setDetail($service->getDescription());
        $appointment->setStatus(0);

        $em->persist($appointment);
        $em->flush();

        try {
            $emailMessage = (new Email())
                ->from('no-reply@bookmycut.com')
                ->to($user->getEmail())
                ->subject('Confirmation de votre rendez-vous')
                ->html("<h2>Confirmation de rendez-vous</h2>
                <p>Bonjour,</p>
                <p>Votre rendez-vous pour <strong>{$service->getName()}</strong> est confirmé.</p>
                <p>Date : <strong>{$dateTime->format('d/m/Y')}</strong></p>
                <p>Heure : <strong>{$dateTime->format('H:i')}</strong></p>
                <p>Prix : <strong>{$service->getPrice()}€</strong></p>
                <p>Merci de votre confiance !</p>");

            $mailer->send($emailMessage);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('warning', 'Votre rendez-vous est confirmé, mais nous n\'avons pas pu envoyer l\'email de confirmation.');
        }

        $session->getFlashBag()->add('success', 'Rendez-vous confirmé pour le ' . $dateTime->format('d/m/Y') . ' à ' . $dateTime->format('H:i') . '. Un email de confirmation vous a été envoyé.');
        return $this->redirectToRoute('app_reservations');
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
