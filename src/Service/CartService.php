<?php

class CartService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepo,
        private ProductRepository $productRepo
    ) {}

    /**
     * Gets or creates cart for user (always returns non-null)
     * Persistence: None (read-only unless modified)
     */
    public function getCart(User $user): Cart
    {
        if ($user->getCart() === null) {
            $user->setCart(new Cart());
            $this->em->persist($user->getCart());
            $this->em->flush();
        }
        return $user->getCart();
    }

    private function findCartItem(Cart $cart, int $productId): ?CartItem
    {
        foreach ($cart->getItems() as $item) {
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
        $cart = $this->getCart($user);
        $product = $this->productRepo->find($productId);
        
        if ($item = $this->findCartItem($cart, $productId)) {
            $item->setQuantity($item->getQuantity() + 1);
        } else {
            $cart->addCartItem(new CartItem($product, 1));
            $this->em->persist($cart->getCartItems()->last());
        }
        
        $this->em->flush();
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
        $this->setQte($user, $productId, 
            max(0, $this->getQte($user, $productId) - 1)
        );
    }


    /**
     * Removes items with zero quantity
     * Persistence: 
     * - Removes zero-qty CartItems
     * - Flushes changes
     */
    public function clearZeroQte(User $user): void
    {
        if ($cart = $this->cartRepo->findOneBy(['user' => $user])) {
            foreach ($cart->getItems() as $item) {
                if ($item->getQuantity() <= 0) {
                    $cart->removeItem($item);
                    $this->em->remove($item);
                }
            }
            $this->em->flush();
        }
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
        $cart = $this->cartRepo->findOneBy(['user' => $user]);
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
            
            $this->em->remove($cart);
            $this->em->flush();
        }
        
        return $result;
    }

}