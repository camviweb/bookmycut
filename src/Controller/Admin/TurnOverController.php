<?php

namespace App\Controller\Admin;

use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/turnover')]
#[IsGranted('ROLE_ADMIN')]
class TurnOverController extends AbstractController
{
    #[Route('/', name: 'app_admin_turnover')]
    public function turnover(AppointmentRepository $appointmentRepository): Response
    {
        // Récupération des rendez-vous et des utilisateurs
        $appointments = $appointmentRepository->findAll();

        // Dates
        $dateNow = new \DateTime();
        $firstDayOfLastMonth = (clone $dateNow)->modify('first day of last month');
        $lastDayOfLastMonth = (clone $dateNow)->modify('last day of last month');
        $firstDayOfCurrentMonth = (clone $dateNow)->modify('first day of this month');
        $lastDayOfCurrentMonth = (clone $dateNow)->modify('last day of this month');

        // Récupérer les rendez-vous du mois dernier et du mois actuel
        $appointmentsLastMonth = $appointmentRepository->findAppointmentsBetweenDates($firstDayOfLastMonth, $lastDayOfLastMonth);
        $appointmentsCurrentMonth = $appointmentRepository->findAppointmentsBetweenDates($firstDayOfCurrentMonth, $lastDayOfCurrentMonth);

        // Initialisation des revenus par jour pour le mois précédent
        $revenusMoisPrecedentParJour = array_fill_keys(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], 0);

        // Calcul des revenus par jour pour le mois précédent
        foreach ($appointmentsLastMonth as $appointment) {
            $day = $appointment->getDate()->format('l'); // Récupérer le jour de la semaine
            $revenusMoisPrecedentParJour[$day] += $appointment->getPrice();
        }

        // Calcul des revenus pour le mois actuel
        $revenusParJour = [];
        foreach ($appointments as $appointment) {
            $day = $appointment->getDate()->format('l');
            $revenusParJour[$day] = ($revenusParJour[$day] ?? 0) + $appointment->getPrice();
        }

        // Total revenu et variation
        $totalRevenu = array_sum($revenusParJour);
        $revenuMoisPrecedent = array_sum($revenusMoisPrecedentParJour);
        $variationRevenu = 0;
        if ($revenuMoisPrecedent > 0) {
            $variationRevenu = (($totalRevenu - $revenuMoisPrecedent) / $revenuMoisPrecedent) * 100;
        }

        // Nombre de clients (actuel et précédent)
        $totalClientsMoisActuel = count(array_unique(array_map(fn($a) => $a->getUser()->getId(), $appointmentsCurrentMonth)));
        $totalClientsMoisPrecedent = count(array_unique(array_map(fn($a) => $a->getUser()->getId(), $appointmentsLastMonth)));

        // Variation clients
        $variationClients = 0;
        if ($totalClientsMoisPrecedent > 0) {
            $variationClients = (($totalClientsMoisActuel - $totalClientsMoisPrecedent) / $totalClientsMoisPrecedent) * 100;
        }

        // Préparer les données pour la vue
        $transactions = [];
        foreach ($appointments as $appointment) {
            $transactions[] = [
                'client' => $appointment->getUser()->getFirstName() . ' ' . $appointment->getUser()->getLastName(),
                'prestation' => $appointment->getService()->getName(),
                'prix' => $appointment->getPrice(),
                'statut' => $appointment->getStatus(),
            ];
        }

        return $this->render('admin/turnover.html.twig', [
            'totalRevenu' => $totalRevenu,
            'variationRevenu' => round($variationRevenu, 1),
            'totalClients' => $totalClientsMoisActuel,
            'variationClients' => round($variationClients, 1),
            'appointments' => $appointments,
            'transactions' => $transactions,
            'revenusLabels' => array_keys($revenusParJour),
            'revenusValeurs' => array_values($revenusParJour),
            'revenusMoisPrecedent' => array_values($revenusMoisPrecedentParJour), // Revenus par jour du mois précédent
        ]);
    }

}
