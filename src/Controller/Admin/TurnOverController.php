<?php
namespace App\Controller\Admin;

use App\Service\Admin\TurnoverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/turnover')]
#[IsGranted('ROLE_ADMIN')]
class TurnOverController extends AbstractController
{
    private TurnoverService $turnoverService;

    public function __construct(TurnoverService $turnoverService)
    {
        $this->turnoverService = $turnoverService;
    }

    #[Route('/', name: 'app_admin_turnover')]
    public function turnover(): Response
    {
        $turnoverData = $this->turnoverService->getTurnoverData();

        return $this->render('admin/turnover.html.twig', $turnoverData);
    }
}
