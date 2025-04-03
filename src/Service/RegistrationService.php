<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class RegistrationService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private FlashBagInterface $flashBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    public function handleRegistration(Request $request): RedirectResponse|array
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

            // Ajout d'un message flash
            $this->flashBag->add('success', 'Votre compte a été créé avec succès ! Connectez-vous pour accéder à votre compte.');

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
