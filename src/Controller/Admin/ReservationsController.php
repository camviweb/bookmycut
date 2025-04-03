<?php

namespace App\Controller\Admin;

use App\Service\Admin\ReservationsService;
use App\Entity\Service;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Appointment;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class ReservationsController extends AbstractController
{
    private ReservationsService $reservationsService;
    

    public function __construct(ReservationsService $reservationsService)
    {
        $this->reservationsService = $reservationsService;
        
    }

    #[Route('/', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        $events = $this->reservationsService->getAllReservations();

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $events,]);

    }
    #[Route('/new', name: 'app_admin_reservations_new')]
    public function new(Request $request): Response
    {
        // Créer le formulaire
        $form = $this->reservationsService->createAppointment($request);

        // Si le formulaire est validé, rediriger
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Le rendez-vous a été créé avec succès.');
            return $this->redirectToRoute('app_admin_reservations'); // Redirige vers la liste des réservations
        }

        // Renvoyer la vue avec le formulaire
        return $this->render('admin/reservations_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/service/{id}', name: 'api_service_details', methods: ['GET'])]
    public function getServiceDetails(Service $service): JsonResponse
    {
        return $this->json([
            'price' => $service->getPrice(),
            'description' => $service->getDescription(),
        ]);
    }

}
