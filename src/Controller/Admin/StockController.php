<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin/stock')]
#[IsGranted('ROLE_ADMIN')]
class StockController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'app_admin_stock')]
    public function stock(): Response
    {
        $produits = $this->productRepository->findAll();

        return $this->render('admin/stock.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/add', name: 'app_admin_stock_add', methods: ['POST'])]
    public function addProducts(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $productId = $request->get('productId');
        $quantity = intval($request->get('selectedQuantity'));

        $product = $this->productRepository->find($productId);

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
}
