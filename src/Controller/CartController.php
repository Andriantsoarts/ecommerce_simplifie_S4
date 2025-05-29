<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/AddToCart/{id}', name: 'add_to_cart')]
    public function addToCart( int $id,Request $request, EntityManagerInterface $em): Response
    {
      if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
      }
      $product = $em->getRepository(Product::class)->find($id);

      if (!$product) {
          $this->addFlash('error', 'Une erreur s\'est produite!');
          return $this->redirectToRoute('app_home');
      }

      $user = $this->getUser();

      $cart = $em->getRepository(Cart::class)->findOneBy(['userOwner' => $user, 'isOrderDone' => false]);

      if (!$cart) {
          $cart = new Cart();
          $cart->setUserOwner($user);
          $cart->setCreatedAt(new \DateTimeImmutable());
          $cart->setUpdatedAt(new \DateTimeImmutable());
          $em->persist($cart);
      } else {
          $cart->setUpdatedAt(new \DateTimeImmutable());
      }

      $existingItem = null;
      foreach ($cart->getCartItems() as $item) {
          if ($item->getProduct()->getId() === $product->getId()) {
              $existingItem = $item;
              break;
          }
      }

      if ($existingItem) {
          $existingItem->setQuantity($existingItem->getQuantity() + 1);
      } else {
          $cartItem = new CartItem();
          $cartItem->setCart($cart);
          $cartItem->setProduct($product);
          $cartItem->setQuantity(1);
          $cartItem->setPrice($product->getPrice());
          $em->persist($cartItem);
      }

      $em->flush();

      $this->addFlash('success', 'Produit ajouté au panier.');

      return $this->redirectToRoute('app_home');
    }
    #[Route('/cart', name: 'app_cart_detail')]
    public function cartDetail(CartRepository $cartRepository, Security $security): Response
    {
      $user = $security->getUser();

      if (!$user) {
        return $this->redirectToRoute('app_login');
      }

      $cart = $cartRepository->findOneBy(['userOwner' => $user]);

      if (!$cart || $cart->getCartItems()->isEmpty()) {
        return $this->render('cart/cartDetail.html.twig', [
          'cart' => [],
          'total' => 0
        ]);
      }

      $cartItems = [];
      $total = 0;

      foreach ($cart->getCartItems() as $cartItem) {
        $cartItemId = $cartItem->getId();
        $product = $cartItem->getProduct();
        $quantity = $cartItem->getQuantity();
        $subtotal = $product->getPrice() * $quantity;
        $total += $subtotal;

        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'itemId' => $cartItemId,
        ];
      }

      return $this->render('cart/cartDetail.html.twig', [
          'cart' => $cartItems,
          'total' => $total
      ]);
    }
    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(
        CartRepository $cartRepository,
        EntityManagerInterface $em,
        Security $security
    ): RedirectResponse {
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour vider votre panier.');
        }

        $cart = $cartRepository->findOneBy(['userOwner' => $user]);

        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $em->remove($item); // Supprime chaque CartItem
            }
            $em->flush();
        }

        $this->addFlash('success', 'Votre panier a été vidé.');

        return $this->redirectToRoute('app_cart_detail');
    }
    #[Route('/cart/remove/{id}', name: 'app_cart_remove_item', methods: ['POST'])]
    public function removeItem(
        int $id,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em,
        Security $security
    ): RedirectResponse {
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre panier.');
        }

        $cartItem = $cartItemRepository->find($id);

        if (!$cartItem || $cartItem->getCart()->getUserOwner() !== $user) {
            throw $this->createNotFoundException('Article introuvable ou non autorisé.');
        }

        $em->remove($cartItem);
        $em->flush();

        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart_detail');
    }
    #[Route('/cart/item/{id}/update', name: 'app_cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(
        Request $request,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $em,
        int $id
    ): Response {
        $cartItem = $cartItemRepository->find($id);

        if (!$cartItem) {
            throw $this->createNotFoundException('Item non trouvé.');
        }

        $direction = $request->request->get('direction');

        if ($direction === 'increment') {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } elseif ($direction === 'decrement') {
            $newQty = $cartItem->getQuantity() - 1;
            if ($newQty <= 0) {
                $em->remove($cartItem);
            } else {
                $cartItem->setQuantity($newQty);
            }
        }

        $em->flush();

        return $this->redirectToRoute('app_cart_detail');
    }
    #[Route ('/count/item', name: 'app_cart_count_item')]
    public function countItem(CartRepository $cartRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['count' => 0]);
        }

        $cart = $cartRepository->findOneBy(['userOwner' => $user, 'isOrderDone' => false]);

        if (!$cart) {
            return $this->json(['count' => 0]);
        }

        $count = count($cart->getCartItems());

        return $this->json(['count' => $count]);
    }

}
