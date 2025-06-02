<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'cat_show')]
	
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/add', name: 'cat_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(): Response
    {
        return $this->render('category/add.html.twig');
    }

    #[Route('/update/{cat_name}', name: 'cat_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function update(string $cat_name): Response
    {
        return new Response("Updated Category {$cat_name}");
    }

    #[Route('/delete/{cat_name}', name: 'cat_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $cat_name): Response
    {
        return new Response("Deleted Category {$cat_name}");
    }
}
