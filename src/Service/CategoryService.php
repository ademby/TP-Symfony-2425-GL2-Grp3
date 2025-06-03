<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CategoryRepository $repo
    ) {}

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->repo->findAll();
    }
    public function getById(int $id): ?Category
    {
        return $this->repo->find($id);
    }
    public function getByName(string $name): ?Category
    {
        return $this->repo->findOneBy(['name' => $name]);
    }

    public function addCategory(Category $category): void
    {
        $this->em->persist($category);
        $this->em->flush();
    }

    public function updateCategory(Category|int $target, ?Category $newData = null): void
    {
        $category = $target instanceof Category ? $target : $this->repo->find($target);

        if ($newData) {
            // Update non-null properties
            if ($newData->getName() !== null) {
                $category->setName($newData->getName());
            }
            if ($newData->getImageURL() !== null) {
                $category->setImageURL($newData->getImageURL());
            }
        }

        $this->em->flush();
    }

    public function deleteCategory(Category|int $category): void
    {
        $category = $category instanceof Category 
            ? $category 
            : $this->repo->find($category);

        if (!$category) {
            return;
        }
        $products = $category->getProducts();
        foreach ($products as $product) {
            $product->setCategory(null);
            $this->em->persist($product);
        }
        $this->em->remove($category);
        $this->em->flush();
    }
}