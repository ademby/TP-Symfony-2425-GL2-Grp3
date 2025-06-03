<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\Service\UploaderService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response,Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Category;
use App\Form\CategoryForm;


#[Route('/category')]
final class CategoryController extends AbstractController
{

    public function __construct(
        private CategoryService $categoryService
    )
    {}

    #[Route('/', name: 'cat_show')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->categoryService->getCategories()
        ]);
    }

    #[Route('/add', name: 'cat_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, UploaderService $uploader, ValidatorInterface $validator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category, [
            'submit_label' => 'Add Category',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imagePath = $uploader->upload($imageFile);
                $category->setImageURL($imagePath);

                $errors = $validator->validate($category);

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error->getPropertyPath() . ': ' . $error->getMessage());
                    }
                } else {
                    $this->categoryService->addCategory($category);
                    $this->addFlash('success', 'Category added successfully!');
                    return $this->redirectToRoute('cat_show');
                }
            } else {
                $this->addFlash('error', 'Please upload an image.');
            }
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/update/{cat_name}', name: 'cat_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(string $cat_name, Request $request, UploaderService $uploader, ValidatorInterface $validator): Response
    {
        $originalCategory = $this->categoryService->getByName($cat_name);
        if (!$originalCategory) {
            $this->addFlash('error', 'Category not found.');
            return $this->redirectToRoute('cat_show');
        }

        // Create empty object to hold the form data (partial update)
        $categoryChanges = new Category();

        $form = $this->createForm(CategoryForm::class, $categoryChanges, [
            'submit_label' => 'Update Category',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Handle image upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imagePath = $uploader->upload($imageFile);
                if($imagePath){$categoryChanges->setImageURL($imagePath);}
            }

            // Validate the changes object
            //$errors = $validator->validate($categoryChanges);
            //if (count($errors) > 0) {
            //    foreach ($errors as $error) {
            //        $this->addFlash('error', $error->getPropertyPath() . ': ' . $error->getMessage());
            //    }
            //}

            // Always apply what we can
            $this->categoryService->updateCategory($originalCategory, $categoryChanges);
            $this->addFlash('success', 'Category updated with available valid data.');

            return $this->redirectToRoute('cat_show');
        }

        return $this->render('category/update.html.twig', [
            'form' => $form->createView(),
            'category' => $originalCategory,
        ]);
    }




    #[Route('/delete/{cat_name}', name: 'cat_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $cat_name): Response
    {
        // Use the service's getByName method instead of directly accessing the repository
        $category = $this->categoryService->getByName($cat_name);

        if (!$category) {
            $this->addFlash('error', 'Category not found.');
            return $this->redirectToRoute('cat_show');
        }

        try {
            // This already correctly uses the service's deleteCategory method
            $this->categoryService->deleteCategory($category);
            $this->addFlash('success', 'Category successfully deleted.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Category could not be deleted.');
        }

        return $this->redirectToRoute('cat_show');
    }
}
