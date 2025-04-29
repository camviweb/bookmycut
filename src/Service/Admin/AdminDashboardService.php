<?php
// src/Service/AdminDashboardService.php
namespace App\Service\Admin;

class AdminDashboardService
{
    public function getDashboardCards(): array
    {
        return [
            [
                'title' => 'Gestion des Stocks',
                'description' => 'Consultez et mettez à jour les stocks de produits.',
                'link' => 'app_admin_stock', // Pas besoin de $this->generateUrl ici, c'est le contrôleur qui s'en charge
                'button_text' => 'Voir les stocks',
                'icon' => 'bi-box-seam'
            ],
            [
                'title' => 'Réservations',
                'description' => 'Gérez les rendez-vous des clients.',
                'link' => 'app_admin_reservations',
                'button_text' => 'Voir les réservations',
                'icon' => 'bi-calendar3'
            ],
            [
                'title' => 'Chiffres d\'affaires',
                'description' => 'Consultez les chiffres d\'affaires et les statistiques.',
                'link' => 'app_admin_turnover',
                'button_text' => 'Voir les chiffres',
                'icon' => 'bi-currency-dollar'
            ]
        ];
    }
}
