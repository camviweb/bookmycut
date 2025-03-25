<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        $cards = [
            [
                'title' => 'Gestion des Stocks',
                'description' => 'Consultez et mettez à jour les stocks de produits.',
                'link' => $this->generateUrl('app_admin_stock'),
                'button_text' => 'Voir les stocks',
                'icon' => 'bi-box-seam'
            ],
            [
                'title' => 'Réservations',
                'description' => 'Gérez les rendez-vous des clients.',
                'link' => $this->generateUrl('app_admin_reservations'),
                'button_text' => 'Voir les réservations',
                'icon' => 'bi-calendar3'
            ],
            [
                'title' => 'Chiffres d\'affaires',
                'description' => 'Consultez les chiffres d\'affaires et les statistiques.',
                'link' => $this->generateUrl('app_admin_turnover'),
                'button_text' => 'Voir les chiffres',
                'icon' => 'bi-currency-dollar'
            ]
        ];

        return $this->render('admin/index.html.twig', [
            'cards' => $cards,
        ]);
    }
}
