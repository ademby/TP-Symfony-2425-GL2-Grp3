<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
final class CartController extends AbstractController
{
    public function __construct(
        public CartService $cartService,
    )
    {}
    #[Route('/', name: 'cart_show')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('User not authenticated or invalid user type.');
        }
        $cart = $this->cartService->getCartItems($user);

        return $this->render('cart/index.html.twig', [
//            'cart' => null
            'cart' => $cart
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add')]
    #[IsGranted('ROLE_USER')]
    public function add(#[MapEntity] Product $product): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('User not authenticated or invalid user type.');
        }
        $cart = $this->cartService->getCartItems($user);
        $this->cartService->addProduct($user, $product->getId());
        return new Response($product->getTitle() . " added to cart");
    }

    #[Route('/remove', name: 'cart_remove')]
    #[IsGranted('ROLE_USER')]
    public function remove(): Response
    {
        return new Response("Removed Product From Cart");
    }

    #[Route('/clear', name: 'cart_clear')]
    #[IsGranted('ROLE_USER')]
    public function clear(): Response
    {
        return new Response("Cleared Cart");
    }

    #[Route('/validate', name: 'cart_validate')]
    #[IsGranted('ROLE_USER')]
    public function validate(): Response
    {
        return new Response("Wanna Proceed To Payment?");
    }
}
