<?php
// src/Controller/Admin/AdminController.php
namespace App\Controller\Admin;

use App\Service\Admin\AdminDashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private $adminDashboardService;

    // Injection du service dans le contrôleur
    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        // Appeler la méthode du service pour récupérer les cartes
        $cards = $this->adminDashboardService->getDashboardCards();

        return $this->render('admin/index.html.twig', [
            'cards' => $cards,
        ]);
    }
}
