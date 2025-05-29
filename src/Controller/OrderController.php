<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
    #[Route('/create-order', name: 'app_order_create')]
    public function create(EntityManagerInterface $em, CartService $cartService, UserRepository $userRepository,CartRepository $cartRepository): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour passer une commande.');
            return $this->redirectToRoute('app_login');
        }


        $cart = $cartRepository->findOneBy([
            'userOwner' => $user,
            'isOrderDone' => false,
        ]);

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_detail');
        }
        $currentUser = $userRepository->find($user);
        $address = $currentUser->getAddress() ?? $currentUser->getAddress2();


        $order = new Order();
        $order->setClient($user);
        $order->setIsConfirmed(false);
        $order->setIsDelivred(false);
        $order->setIsReturned(false);
        $order->setStatus('en_attente');
        $order->setDeliveryAddress($address);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setUpdatedAt(new \DateTimeImmutable());
        $cart->setIsOrderDone(true);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($cart);
        foreach ($cart->getCartItems() as $item) {
            $order->addItem($item);
            $item->setOrderOwn($order);
        }

        $em->persist($order);
        $em->flush();

        $cartService->clearCart();
        return $this->redirectToRoute('order_summary', ['id' => $order->getId()]);

    }

    #[Route('/order/resume/{id}', name: 'order_summary')]
    public function summary(int $id, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $order = $em->getRepository(Order::class)->find($id);

        if (!$order || $order->getClient() !== $user) {
            throw $this->createNotFoundException("Commande introuvable ou accès interdit.");
        }
        foreach ($order->getItems() as $item) {
            
        }


        return $this->render('order/order.html.twig', [
            'order' => $order,
        ]);
    }
    #[Route('/order/confirmation/{id}', name: 'order_confirm', methods: ['POST'])]
    public function confirm(Order $order, Security $security, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();

        if ($order->getClient() !== $user) {
            throw $this->createAccessDeniedException();
        }

        // Marquer comme validée (ex: statut)
        $order->setStatus('En cours de traitement');
        $em->flush();

        return $this->render('order/order_confirmed.html.twig', [
            'order' => $order
        ]);
    }


}
