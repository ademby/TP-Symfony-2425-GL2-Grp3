<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderRepository $orderRepo,
        private ProductRepository $productRepo
    ) {}

    /**
     * Creates order from cart validation data
     * @param array $cartData Format: [productId => ['qte' => X, 'price' => Y]]
     */
    public function create(array $cartData, User $user): Order
    {
        $order = new Order();
        $order->setUser($user);

        $total = 0;
        foreach ($cartData as $pid => $item) {
            $product = $this->productRepo->find($pid);
            $orderItem = new OrderItem(
                product: $product,
                quantity: $item['qte'],
                unitPrice: $item['price']
            );
            $order->addOrderItem($orderItem);
            $total += ($item['price'] * $item['qte']);
        }

        $order->setTotal($total); // Set calculated total
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orderRepo->findAll();
    }

    public function getStatus(Order $order): string
    {
        return $order->getStatus(); // ORM default handles initial status
    }

    public function setStatus(Order $order, string $status): void
    {
        $order->setStatus($status);
        $this->em->flush();
    }

    public function delete(Order $order): void
    {
        $this->em->remove($order);
        $this->em->flush(); // Orphan removal handled by ORM
    }
}




