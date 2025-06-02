<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $repo
    ) {}

    /**
     * @return Product[]
     */
    public function getProducts(
        ?Category $category = null,
        ?int $page = null,
        ?int $limit = null
    ): array {
        return $this->repo->findByFilters(
            category: $category,
            page: $page,
            limit: $limit
        );
    }

    public function addProduct(Product $product): void
    {
        $this->em->persist($product);
        $this->em->flush();
    }

    public function updateProduct(Product|int $target, Product $newData): void
    {
        $product = $target instanceof Product ? $target : $this->repo->find($target);

        if ($newData->getTitle() !== null) $product->setTitle($newData->getTitle());
        if ($newData->getDescription() !== null) $product->setDescription($newData->getDescription());
        if ($newData->getImageURL() !== null) $product->setImageURL($newData->getImageURL());
        if ($newData->getPrice() !== null) $product->setPrice($newData->getPrice());
        if (!empty($newData->getProperties())) $product->setProperties($newData->getProperties());
        if ($newData->getCategory() !== null) $product->setCategory($newData->getCategory());

        $this->em->flush();
    }

    public function deleteProduct(Product|int $product): void
    {
        $product = $product instanceof Product ? $product : $this->repo->find($product);
        $this->em->remove($product);
        $this->em->flush();
    }
}
