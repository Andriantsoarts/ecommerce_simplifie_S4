<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(OrderRepository $orderRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }else {

            $orders = $orderRepository->findBy([],['createdAt' => 'DESC']);

            return $this->render('dashboard/index.html.twig', [
            'orders' => $orders,
            ]);
        }
    }
}
