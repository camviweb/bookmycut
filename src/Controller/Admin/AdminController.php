<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/stock', name: 'admin_stock')]
    public function stock(): Response
    {
        $produits = [
            [
                'nom' => 'Shampoing',
                'marque' => 'Kérastase',
                'description' => 'Après-shampoing L\'Oréal',
                'quantite' => 9,
                'prix_unitaire' => 30,
            ],
            [
                'nom' => 'Shampoing',
                'marque' => 'Madara',
                'description' => 'Kérastase',
                'quantite' => 3,
                'prix_unitaire' => 45,
            ],
            [
                'nom' => 'Shampoing',
                'marque' => 'Kérastase',
                'description' => '',
                'quantite' => 5,
                'prix_unitaire' => 29,
            ],
            [
                'nom' => '',
                'marque' => '',
                'description' => '',
                'quantite' => 2,
                'prix_unitaire' => 25,
            ],
        ];

        return $this->render('admin/stock.html.twig', [
            'produits' => $produits,
        ]);
    }
}