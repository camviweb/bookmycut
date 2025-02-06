<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/stock', name: 'app_admin_stock')]
    public function stock(): Response
    {
        $produits = [
            [
                'nom' => 'Shampoing',
                'marque' => 'L\'Oréal',
                'description' => 'Shampoing nourrissant',
                'quantite' => 10,
                'prix_unitaire' => 30,
            ],
            [
                'nom' => 'Après-shampoing',
                'marque' => 'Madara',
                'description' => 'Après-shampoing hydratant',
                'quantite' => 8,
                'prix_unitaire' => 45,
            ],
            [
                'nom' => 'Shampoing',
                'marque' => 'Kérastase',
                'description' => 'Shampoing réparateur',
                'quantite' => 5,
                'prix_unitaire' => 29,
            ],
            [
                'nom' => 'Huile capillaire',
                'marque' => 'L\'Oréal',
                'description' => 'Huile nourrissante pour cheveux',
                'quantite' => 3,
                'prix_unitaire' => 25,
            ],
        ];

        return $this->render('admin/stock.html.twig', [
            'produits' => $produits,
        ]);
    }
}