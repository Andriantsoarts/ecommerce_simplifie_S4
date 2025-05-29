<?php
namespace App\Service;

use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    private $cartItemRepository;
    private $cartRepository;
    private $security;

    public function __construct(CartItemRepository $cartItemRepository,CartRepository $cartRepository , Security $security)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->cartRepository = $cartRepository;
        $this->security = $security;
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
}
