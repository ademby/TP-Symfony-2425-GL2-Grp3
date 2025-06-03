<?php

namespace App\Controller;

use App\Entity\{Product,Category};
use App\Service\{CategoryService,ProductService,UploaderService};
use App\Form\ProductForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response,Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    #[Route('/add', name: 'prod_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, UploaderService $uploader, ValidatorInterface $validator): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product, [
            'submit_label' => 'Add Product',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getCategory() === null) {} // may need later
            
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagePath = $uploader->upload($imageFile);
                $product->setImageURL($imagePath);

                $errors = $validator->validate($product);

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error->getPropertyPath() . ': ' . $error->getMessage());
                    }
                } else {
                    $this->productService->addProduct($product);
                    $this->addFlash('success', 'Product added successfully!');
                    return $this->redirectToRoute('prod_show_all');
                }
            } else {
                $this->addFlash('error', 'Please upload a product image.');
            }
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

#[Route('/update/{id}', name: 'prod_update', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
public function update(int $id, Request $request, UploaderService $uploader, ValidatorInterface $validator): Response
{
    $originalProduct = $this->productService->getProduct($id);
    if (!$originalProduct) {
        $this->addFlash('error', 'Product not found.');
        return $this->redirectToRoute('prod_list');
    }

    // Create empty changes object
    $productChanges = new Product();

    // Inject original values (except imageURL which will be handled separately)
    $productChanges->setTitle($originalProduct->getTitle());
    $productChanges->setDescription($originalProduct->getDescription());
    $productChanges->setPrice($originalProduct->getPrice());
    $productChanges->setProperties($originalProduct->getProperties());
    $productChanges->setCategory($originalProduct->getCategory());
    // Do NOT set imageURL here — image handled by uploader and merge logic

    $form = $this->createForm(ProductForm::class, $productChanges, [
        'submit_label' => 'Update Product',
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload separately
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $imagePath = $uploader->upload($imageFile);
            $productChanges->setImageURL($imagePath);
        }

        // Validate the merged product before applying update
        $errors = $validator->validate($productChanges);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getPropertyPath() . ': ' . $error->getMessage());
            }
        } else {
            // Apply selective merge update
            $this->productService->updateProduct($originalProduct, $productChanges);

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('prod_show_prod', ['id' => $originalProduct->getId()]);
        }
    }

    return $this->render('product/update.html.twig', [
        'form' => $form->createView(),
        'product' => $originalProduct,
    ]);
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
