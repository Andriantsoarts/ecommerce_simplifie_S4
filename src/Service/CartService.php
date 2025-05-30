<?php
namespace App\Service;

use App\Entity\Cart;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    private $cartItemRepository;
    private $cartRepository;
    private $security;
    private $em;

    public function __construct(CartItemRepository $cartItemRepository,CartRepository $cartRepository , Security $security,EntityManagerInterface $em)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->cartRepository = $cartRepository;
        $this->security = $security;
        $this->em = $em;
    }

    public function getCartItemCount(): int
    {
        $user = $this->security->getUser();

        if (!$user) {
            return 0;
        }

        $cart = $this->cartRepository->findBy(['userOwner' => $user, 'isOrderDone' => false]);
        if (!$cart) {
            return 0;
        }
        $cartItems = $this->cartItemRepository->findBy(['cart' => $cart]);
        if (!$cartItems) {
            return 0;
        }
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item->getQuantity();
        }

        return $total;
    }
    public function clearCart(): void
    {
        $user = $this->security->getUser();

        $carts = $this->cartRepository->findBy([
            'userOwner' => $user,
            'isOrderDone' => true,
        ]);

        foreach ($carts as $cart) {
            foreach ($cart->getCartItems() as $item) {
                $item->setCart(null);
            }
            $this->em->remove($cart);
        }

        $this->em->flush();
    }

}
