<?php

namespace App\DataFixtures;

use App\Entity\{Product,Category};
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\UploaderService;

use App\DataFixtures\CategoryFixtures;


class ProductFixtures extends Fixture
{
    public function __construct(
        private KernelInterface $kernel,
        private UploaderService $uploaderService
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->seed(0);

        // Products with categories
        $this->loadCategoryProducts($manager, 'computer', 30);
        $this->loadCategoryProducts($manager, 'laptop', 30);
        $this->loadCategoryProducts($manager, 'monitor', 30);
        
        // Uncategorized products
        $this->loadUncategorizedProducts($manager, 'mouse', 5);
        $this->loadUncategorizedProducts($manager, 'keyboard', 5);
    }

    private function loadCategoryProducts(ObjectManager $manager, string $cat_name, int $count): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product
                ->setTitle(ucfirst($faker->words(3, true)))
                ->setDescription($faker->boolean(70) ? $faker->sentences(3, true) : null)
                ->setPrice($faker->numberBetween(100, 9000))
                ->setProperties($this->generateProperties($faker))
                ->setCategory($this->getReference("category_{$cat_name}", Category::class));

            $imageId = $faker->numberBetween(1, 6);
            $imagePath = $this->kernel->getProjectDir()."/dummy-data/{$cat_name}/{$cat_name}_{$imageId}.png";
            
            if (file_exists($imagePath)) {
                $product->setImageURL($this->uploaderService->copy($imagePath));
            }

            $manager->persist($product);
        }
        
        $manager->flush();
    }

    private function loadUncategorizedProducts(ObjectManager $manager, string $productType, int $count): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < $count; $i++) {
            $product = new Product();
            $product
                ->setTitle(ucfirst($faker->words(3, true)))
                ->setDescription($faker->boolean(50) ? $faker->sentences(2, true) : null)
                ->setPrice($faker->numberBetween(50, 500))
                ->setProperties($this->generateProperties($faker));

            $imagePath = $this->kernel->getProjectDir()."/dummy-data/{$productType}.png";
            
            if (file_exists($imagePath)) {
                $product->setImageURL($this->uploaderService->copy($imagePath));
            }

            $manager->persist($product);
        }
        
        $manager->flush();
    }

    private function generateProperties($faker): array
    {
        $props = [];
        $count = $faker->numberBetween(2, 5);
        
        for ($i = 0; $i < $count; $i++) {
            $props[$faker->word] = $faker->numberBetween(1, 100);
        }
        
        return $props;
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}