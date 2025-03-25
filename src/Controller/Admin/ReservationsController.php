<?php

namespace App\Controller\Admin;

use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class ReservationsController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    #[Route('/', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        $reservations = $this->appointmentRepository->findAll();

        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'title' => $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName(),
                'client' => $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName(),
                'service' => $reservation->getService()->getName(),
                'description' => $reservation->getDetail(),
                'price' => $reservation->getPrice(),
                'status' => $reservation->getStatus(),
                'duration' => $reservation->getService()->getDuration(),
                'start' => $reservation->getDate()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getDate()->modify('+' . $reservation->getService()->getDuration() . ' minutes')->format('Y-m-d\TH:i:s'),
            ];
        }

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $events,
        ]);
    }
}
