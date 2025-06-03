<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/product')]
final class ProductController extends AbstractController
{
    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService
    )
    {
    }

    #[Route('/add', name: 'prod_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(): Response
    {
        return $this->render('product/add.html.twig');
    }

    #[Route('/update', name: 'prod_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function update(): Response
    {
        return $this->render('product/update.html.twig');
    }
    #[Route('/delete/{id}', name: 'prod_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): Response
    {
        try {
            $this->productService->deleteProduct($id);
            return $this->redirectToRoute('prod_show_all');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Product not found or could not be deleted.');
            return $this->redirectToRoute('prod_show_all');
        }
    }

    #[Route('/', name: 'prod_show_all')]
    public function show_all(): Response
    {
        return $this->render('product/show_many.html.twig', [
            'products' => $this->productService->getProducts() // Testing the service
        ]);
    }

    #[Route('/id/{id}', name: 'prod_show_prod', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        return $this->render('product/show_prod.html.twig', [
            'product' => $this->productService->getProduct($id)
        ]);
    }

    #[Route('/category/{cat_name}', name: 'prod_show_cat')]
    public function show_cat(string $cat_name): Response
    {
        // Check if the category exists
        $category = $this->categoryService->getByName($cat_name);
        if ( $category == null) {
            throw $this->createNotFoundException("Category '$cat_name' does not exist.");
        }
        return $this->render('product/show_many.html.twig', [
            'category' => $category,
            'products' => $this->productService->getProducts($category)
        ]);
    }
}
