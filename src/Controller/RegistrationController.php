<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $error = null;

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et a des erreurs
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $formError) {
                $error .= $formError->getMessage() . '<br>';
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Rôle de l'utilisateur par défaut
            $user->setRole('ROLE_USER');

            // Sauvegarde de l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection après inscription
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/registration.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

}
