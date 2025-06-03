<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository         $cartRepository,
        private ProductRepository      $productRepository
    ) {}

    /**
     * Gets or creates cart for user (always returns non-null)
     * Persistence: None (read-only unless modified)
     */
    public function getCartItems(User $user): Collection
    {
        if ($user->getCartItems() === null) {
            $user->setCartItems(new ArrayCollection());
            $this->entityManager->persist($user->getCartItems());
            $this->entityManager->flush();
        }
        return $user->getCartItems();
    }

    private function findCartItem(Collection $cartItems, int $productId): ?CartItem
    {
        foreach ($cartItems as $item) {
            if ($item->getProduct()->getId() === $productId) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Adds/updates product in cart
     * Persistence:
     * - Creates+persists Cart if new
     * - Creates+persists CartItem if new product
     * - Updates existing CartItem
     */
    public function addProduct(User $user, int $productId): void
    {
        $cartItems = $this->getCartItems($user);
        $product = $this->productRepository->find($productId);

        if ($item = $this->findCartItem($cartItems, $productId)) {
            $item->setQuantity($item->getQuantity() + 1);
        } else {
            $user->addCartItem(new CartItem($product, 1));
            $this->entityManager->persist($cartItems->last());
        }

        $this->entityManager->flush();
    }
    /*
     * WARNING: Avoid
     */
    public function incQte(User $user, int $productId): void
    {
        $this->addProduct($user, $productId);
    }

    /**
     * WARNING: Avoid
     */
    public function decQte(User $user, int $productId): void
    {
        $cartItems = $this->getCartItems($user);
        $cartItem  = $this->findCartItem($cartItems,$productId);
        $cartItems->removeElement($cartItem);
        if($cartItem->getQuantity() > 1) {;
//            $this->
        }
        $user->setCartItems($cartItems);
    }


    /**
     * Finalizes cart contents
     * Persistence:
     * - Deletes Cart and all CartItems
     * - Flushes immediately
     * @return array [productId => ['qte' => X, 'price' => Y]]
     */
    public function validate(User $user): array
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        $result = [];

        if ($cart) {
            foreach ($cart->getItems() as $item) {
                if ($item->getQuantity() > 0) {
                    $result[$item->getProduct()->getId()] = [
                        'qte' => $item->getQuantity(),
                        'price' => $item->getProduct()->getPrice()
                    ];
                }
            }

            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        }

        return $result;
    }

}

// USAGE
// // Checkout
// $cartData = $cartService->validate($user);
// $order = $orderService->create($cartData, $user);
//
// // Admin
// $orderService->setStatus($order, 'shipped');
