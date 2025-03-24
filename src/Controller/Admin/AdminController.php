<?php

namespace App\Controller\Admin;

use App\Repository\AppointmentRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private AppointmentRepository $appointmentRepository;
    private ProductRepository $productRepository;

    public function __construct(AppointmentRepository $appointmentRepository, ProductRepository $productRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->productRepository = $productRepository;
    }

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
/*            [
                'title' => 'Utilisateurs',
                'description' => 'Gérez les comptes des clients et employés.',
                'link' => '#',
                'button_text' => 'Gérer les utilisateurs',
                'icon' => 'bi-people'
            ],
*/
        ];

        return $this->render('admin/index.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/stock', name: 'app_admin_stock')]
    public function stock(): Response
    {
        $produits = $this->productRepository->findAll();

        return $this->render('admin/stock.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/stock', name: 'app_admin_stock_add', methods: ['POST'])]
    public function addProducts(Request $request, EntityManagerInterface $em, ProductRepository $productRepository): RedirectResponse
    {
        $productId = $request->get('productId');
        $quantity = intval($request->get('selectedQuantity'));

        $product = $productRepository->findOneBy(['id' => $productId]);

        if (!$product) {
            $this->addFlash('danger', 'Produit non trouvé.');
            return $this->redirectToRoute('app_admin_stock');
        }

        $product->setQuantity($product->getQuantity() + $quantity);
        $em->persist($product);
        $em->flush(); 

        $this->addFlash('success', 'Vous avez acheté le produit.');

        return $this->redirectToRoute('app_admin_stock');
    }


    #[Route('/chiffres-affaires', name: 'app_admin_chiffres_affaires')]
    public function chiffresAffaires(AppointmentRepository  $appointmentRepository): Response
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

        return $this->render('admin/chiffres_affaires.html.twig', [
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

    #[Route('/reservations', name: 'app_admin_reservations')]
    public function reservations(): Response
    {
        $reservations = $this->appointmentRepository->findAll();

        // Convertir les réservations au format attendu par FullCalendar
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
