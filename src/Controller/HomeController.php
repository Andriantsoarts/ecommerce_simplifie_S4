<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CartService $cartService): Response
    {   
        $cartItemCount = $cartService->getCartItemCount();
        return $this->render('home/index.html.twig',[
            'products' => $productRepository->findAll(),
            'cartItemCount' => $cartItemCount,
        ]);
    }
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function getAllProduct(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
}
