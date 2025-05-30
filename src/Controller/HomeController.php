<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductRepository $productRepository,CategoryRepository $categoryRepository , CartService $cartService): Response
    {
        $search = $request->query->get('search'); // string|null
        $categoryIdRaw = $request->query->get('category'); // string|null
        $categoryId = is_numeric($categoryIdRaw) ? (int) $categoryIdRaw : null;

        $products = $productRepository->findBySearchAndCategory($search, $categoryId);
        $cartItemCount = $cartService->getCartItemCount();
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'cartItemCount' => $cartItemCount,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
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
