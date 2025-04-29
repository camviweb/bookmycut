<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RegistrationService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private MailerInterface $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        MailerInterface $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    public function handleRegistration(Request $request, SessionInterface $session): RedirectResponse|array
    {
        $user = new User();
        $form = $this->formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Rôle par défaut
            $user->setRole('ROLE_USER');

            // Sauvegarde en base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Envoi de l'email de confirmation
            try {
                $confirmationEmail = (new Email())
                    ->from('no-reply@bookmycut.com')
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre inscription')
                    ->html("<h2>Bienvenue sur BookMyCut !</h2>
                            <p>Votre compte a été créé avec succès.</p>");
                $this->mailer->send($confirmationEmail);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('warning', 'Votre compte a été créé, mais nous n\'avons pas pu envoyer l\'email de confirmation.');
            }

            // Ajout d'un message flash
            $session->getFlashBag()->add('success', 'Votre compte a été créé avec succès ! Un email vous a été envoyé. Connectez-vous dès maintenant !');

            return new RedirectResponse($this->router->generate('app_home'));
        }

        return [
            'form' => $form->createView(),
            'error' => $this->getFormErrors($form),
        ];
    }

    private function getFormErrors($form): ?string
    {
        $errorMessages = [];

        foreach ($form->getErrors(true) as $formError) {
            $errorMessages[] = $formError->getMessage();
        }

        return $errorMessages ? implode('<br>', $errorMessages) : null;
    }
}