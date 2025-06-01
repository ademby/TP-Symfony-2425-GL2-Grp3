<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route('/', name: 'cart_show')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }
    #[Route('/add', name: 'cart_add')]
    public function add()
    {
        return new Response("Added Product To Cart");
    }

    #[Route('/validate', name: 'cart_validate')]
    public function validate()
    {
        return new Response("Are you sure ? ... <br> We sent you an email, our agent will talk to you soon.");
    }
}
