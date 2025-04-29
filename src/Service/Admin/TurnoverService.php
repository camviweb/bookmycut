<?php
namespace App\Service\Admin;

use App\Repository\AppointmentRepository;
use DateTime;

class TurnoverService
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getTurnoverData(): array
    {
        $appointments = $this->appointmentRepository->findAll();

        $dateNow = new DateTime();
        $firstDayOfLastMonth = (clone $dateNow)->modify('first day of last month');
        $lastDayOfLastMonth = (clone $dateNow)->modify('last day of last month');
        $firstDayOfCurrentMonth = (clone $dateNow)->modify('first day of this month');
        $lastDayOfCurrentMonth = (clone $dateNow)->modify('last day of this month');

        $appointmentsLastMonth = $this->appointmentRepository->findAppointmentsBetweenDates($firstDayOfLastMonth, $lastDayOfLastMonth);
        $appointmentsCurrentMonth = $this->appointmentRepository->findAppointmentsBetweenDates($firstDayOfCurrentMonth, $lastDayOfCurrentMonth);

        $revenusMoisPrecedentParJour = array_fill_keys(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], 0);

        foreach ($appointmentsLastMonth as $appointment) {
            $day = $appointment->getDate()->format('l');
            $revenusMoisPrecedentParJour[$day] += $appointment->getPrice();
        }

        $revenusParJour = [];
        foreach ($appointments as $appointment) {
            $day = $appointment->getDate()->format('l');
            $revenusParJour[$day] = ($revenusParJour[$day] ?? 0) + $appointment->getPrice();
        }

        $totalRevenu = array_sum($revenusParJour);
        $revenuMoisPrecedent = array_sum($revenusMoisPrecedentParJour);
        $variationRevenu = $revenuMoisPrecedent > 0 ? (($totalRevenu - $revenuMoisPrecedent) / $revenuMoisPrecedent) * 100 : 0;

        $totalClientsMoisActuel = count(array_unique(array_map(fn($a) => $a->getUser()->getId(), $appointmentsCurrentMonth)));
        $totalClientsMoisPrecedent = count(array_unique(array_map(fn($a) => $a->getUser()->getId(), $appointmentsLastMonth)));

        $variationClients = $totalClientsMoisPrecedent > 0 ? (($totalClientsMoisActuel - $totalClientsMoisPrecedent) / $totalClientsMoisPrecedent) * 100 : 0;

        $transactions = array_map(fn($a) => [
            'client' => $a->getUser()->getFirstName() . ' ' . $a->getUser()->getLastName(),
            'prestation' => $a->getService()->getName(),
            'prix' => $a->getPrice(),
            'statut' => $a->getStatus(),
        ], $appointments);

        return [
            'totalRevenu' => $totalRevenu,
            'variationRevenu' => round($variationRevenu, 1),
            'totalClients' => $totalClientsMoisActuel,
            'variationClients' => round($variationClients, 1),
            'transactions' => $transactions,
            'revenusLabels' => array_keys($revenusParJour),
            'revenusValeurs' => array_values($revenusParJour),
            'revenusMoisPrecedent' => array_values($revenusMoisPrecedentParJour),
        ];
    }
}
