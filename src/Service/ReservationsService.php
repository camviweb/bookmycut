<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ReservationsService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private AppointmentRepository $appointmentRepository;
    private ServiceRepository $serviceRepository;
    private UserRepository $userRepository;
    private MailerInterface $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        AppointmentRepository $appointmentRepository,
        ServiceRepository $serviceRepository,
        UserRepository $userRepository,
        MailerInterface $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->appointmentRepository = $appointmentRepository;
        $this->serviceRepository = $serviceRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    public function createAppointment(
        Request $request,
        SessionInterface $session,
        string $date,
        string $horaire,
        string $prestationId,
        string $email
    ): string {
        // Validation du service
        $service = $this->serviceRepository->find($prestationId);
        if (!$service) {
            $session->getFlashBag()->add('danger', 'Prestation introuvable.');
            return 'app_reservations';
        }

        // Création de l'objet DateTime
        $dateTime = new \DateTime("$date $horaire");

        // Vérification si un rendez-vous existe déjà
        $existingAppointment = $this->appointmentRepository->findOneBy(['date' => $dateTime]);
        if ($existingAppointment) {
            $session->getFlashBag()->add('danger', 'Un rendez-vous existe déjà pour cette date et heure.');
            return 'app_reservations';
        }

        // Recherche de l'utilisateur
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            // Création de l'utilisateur si nécessaire
            $user = new User();
            $user->setEmail($email);
            $user->setLastName($request->get('lastname'));
            $user->setFirstname($request->get('firstname'));
            $user->setPhone($request->get('phone'));
            $user->setRole('ROLE_USER');

            $password = bin2hex(random_bytes(4)); // Mot de passe temporaire
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));

            $this->entityManager->persist($user);

            // Envoi de l'email de confirmation
            try {
                $confirmationEmail = (new Email())
                    ->from('no-reply@bookmycut.com')
                    ->to($user->getEmail())
                    ->subject('Votre compte a été créé')
                    ->html("<h2>Bienvenue sur notre plateforme !</h2>
                            <p>Votre compte a été créé automatiquement lors de votre réservation.</p>
                            <p>Voici votre mot de passe temporaire : <strong>$password</strong></p>
                            <p>Veuillez le modifier dès votre première connexion.</p>");
                $this->mailer->send($confirmationEmail);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('warning', 'Votre compte a été créé, mais nous n\'avons pas pu envoyer l\'email de confirmation.');
            }
        }

        // Création du rendez-vous
        $appointment = new Appointment();
        $appointment->setDate($dateTime);
        $appointment->setService($service);
        $appointment->setUser($user);
        $appointment->setPrice($service->getPrice());
        $appointment->setDetail($service->getDescription());
        $appointment->setStatus(0);

        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        // Envoi de l'email de confirmation du rendez-vous
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
            $this->mailer->send($emailMessage);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('warning', 'Votre rendez-vous est confirmé, mais nous n\'avons pas pu envoyer l\'email de confirmation.');
        }

        // Message de succès
        $session->getFlashBag()->add('success', 'Rendez-vous confirmé pour le ' . $dateTime->format('d/m/Y') . ' à ' . $dateTime->format('H:i') . '. Un email de confirmation vous a été envoyé.');

        return 'app_reservations';
    }
}
