<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
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
                'link' => $this->generateUrl('app_admin_chiffres_affaires'),
                'button_text' => 'Voir les chiffres',
                'icon' => 'bi-currency-dollar'
            ],
            [
                'title' => 'Utilisateurs',
                'description' => 'Gérez les comptes des clients et employés.',
                'link' => '#',
                'button_text' => 'Gérer les utilisateurs',
                'icon' => 'bi-people'
            ],
        ];

        return $this->render('admin/index.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/stock', name: 'app_admin_stock')]
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

    #[Route('/chiffres-affaires', name: 'app_admin_chiffres_affaires')]
    public function chiffresAffaires(): Response
    {
        // Données fictives
        $revenusParJour = [
            'Dimanche' => 2000, 'Lundi' => 1000, 'Mardi' => 2300,
            'Mercredi' => 1500, 'Jeudi' => 800, 'Vendredi' => 2000, 'Samedi' => 2500,
        ];
        $revenusMoisPrecedent = [
            'Dimanche' => 1800, 'Lundi' => 1200, 'Mardi' => 2100,
            'Mercredi' => 1700, 'Jeudi' => 900, 'Vendredi' => 1900, 'Samedi' => 2300,
        ];

        // Chiffres globaux
        $totalRevenu = 166580;
        $totalClients = 25;

        // Chiffres du mois précédent (valeurs fictives)
        $revenuMoisPrecedent = 158500;
        $clientsMoisPrecedent = 27;

        // Calcul de la variation en pourcentage
        $variationRevenu = (($totalRevenu - $revenuMoisPrecedent) / $revenuMoisPrecedent) * 100;
        $variationClients = (($totalClients - $clientsMoisPrecedent) / $clientsMoisPrecedent) * 100;

        return $this->render('admin/chiffres_affaires.html.twig', [
            'totalRevenu' => $totalRevenu,
            'variationRevenu' => round($variationRevenu, 1), // Arrondi à 1 décimale
            'totalClients' => $totalClients,
            'variationClients' => round($variationClients, 1),
            'transactions' => [
                ['client' => 'Eleanor Pena', 'prestation' => 'Coupe femme', 'prix' => 30, 'statut' => 'Fait'],
                ['client' => 'Wade Warren', 'prestation' => 'Barbe', 'prix' => 20, 'statut' => 'À venir'],
                ['client' => 'Brooklyn Simmons', 'prestation' => 'Permanente', 'prix' => 50, 'statut' => 'Annulé'],
                ['client' => 'Kathryn Murphy', 'prestation' => 'Coupe homme', 'prix' => 25, 'statut' => 'Fait'],
            ],
            'revenusLabels' => array_keys($revenusParJour),
            'revenusValeurs' => array_values($revenusParJour),
            'revenusMoisPrecedent' => array_values($revenusMoisPrecedent),
        ]);
    }

    #[Route('/reservations', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        $reservations = [
            // Coupes Hommes (Bleu)
            [
                'title' => 'Coupe homme - Jean Dupont',
                'start' => '2025-02-06T09:00:00',
                'end' => '2025-02-06T09:30:00',
                'color' => '#3498db',
            ],
            [
                'title' => 'Coupe homme - Paul Martin',
                'start' => '2025-02-07T14:00:00',
                'end' => '2025-02-07T14:30:00',
                'color' => '#3498db',
            ],

            // Coupes Femmes (Violet)
            [
                'title' => 'Coupe femme - Sophie Lemoine',
                'start' => '2025-02-07T10:00:00',
                'end' => '2025-02-07T10:45:00',
                'color' => '#9b59b6',
            ],
            [
                'title' => 'Coupe femme - Clara Moreau',
                'start' => '2025-02-08T11:00:00',
                'end' => '2025-02-08T11:45:00',
                'color' => '#9b59b6',
            ],

            // Colorations (Jaune)
            [
                'title' => 'Coloration - Mélanie Dubois',
                'start' => '2025-02-08T12:00:00',
                'end' => '2025-02-08T13:30:00',
                'color' => '#f1c40f',
            ],
            [
                'title' => 'Coloration - Emma Bernard',
                'start' => '2025-02-09T15:00:00',
                'end' => '2025-02-09T16:30:00',
                'color' => '#f1c40f',
            ],

            // Autres prestations
            [
                'title' => 'Barbe - Thomas Garnier',
                'start' => '2025-02-09T11:30:00',
                'end' => '2025-02-09T12:00:00',
                'color' => '#2ecc71', // Vert
            ],
            [
                'title' => 'Permanente - Laura Petit',
                'start' => '2025-02-07T16:00:00',
                'end' => '2025-02-07T17:30:00',
                'color' => '#9b59b6', // Violet
            ],
        ];

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

}       
