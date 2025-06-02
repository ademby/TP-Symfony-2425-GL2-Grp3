<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Service\UploaderService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

class ProductFixture extends Fixture
{
    private KernelInterface $kernel;
    private UploaderService $uploaderService;

    public function __construct(
        KernelInterface $kernel,
        UploaderService $uploaderService)
    {
        $this->kernel = $kernel;
        $this->uploaderService = $uploaderService;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCategories($manager);
//        $this->loadProductsOfCategory($manager, "laptop");
//        $this->loadProductsOfCategory($manager, "computer");
//        $this->loadProductsOfCategory($manager, "monitor");
//        $this->loadProductNoCategory($manager, "mouse");
//        $this->loadProductNoCategory($manager, "keyboard");

    }

    public function loadCategories(ObjectManager $manager): void
    {
        $categories = ["computer", "laptop", "monitor"];
        foreach ($categories as $catName) {
            $category = new Category();
            $category->setName($catName);
            $imageName = "cat_" . $catName . '.jpg';
            $imagePath = $this->kernel->getProjectDir() . "/assets/images/" . $imageName;
            $url = $this->uploaderService->copy($imagePath);
            $category->setImageURL($url);
            $manager->persist($category);
        }
        $manager->flush();
    }

    public function loadProductsOfCategory(ObjectManager $manager, string $cat_name): void
    {
        $faker = \Faker\Factory::create();
        $price_min = 100;
        $price_max = 9000;

        for ($i = 0; $i < 30; $i++) {
            $product = new Product();
            $product->setTitle($faker->words(3, true));
            $product->setDescription($faker->words(rand(0, 12), true));
            $product->setPrice(rand($price_min, $price_max));

            $properties = [];
            for ($j = 0; $j < rand(2, 5); $j++) {
                $properties[$faker->word] = rand(1, 100);
            }
            $product->setProperties($properties);

            $category = $manager->getRepository(Category::class)->findOneBy(['name' => $cat_name]);
            if ($category) {
                $product->setCategory($category);
            }

            $image_name = $product->getTitle().rand(1000,5243) . '.jpg';
            $imagePath = $this->kernel->getProjectDir() . '/assets/images/' . $image_name;

            if (file_exists($imagePath)) {
                $url = $this->uploaderService->copy($imagePath);
                $product->setImageURL($url);
            }

            $manager->persist($product);
        }

        $manager->flush();

    }

    public function loadProductNoCategory(ObjectManager $manager, string $prod_name): void
    {
        $faker = \Faker\Factory::create();
        $price_min = 100;
        $price_max = 9000;

        for ($i = 0; $i < 5; $i++) {
            $product = new Product();
            $product->setTitle($faker->words(3, true));
            $product->setDescription($faker->words(rand(0, 12), true));
            $product->setPrice(rand($price_min, $price_max));

            $properties = [];
            for ($j = 0; $j < rand(2, 5); $j++) {
                $properties[$faker->word] = rand(1, 100);
            }
            $product->setProperties($properties);

            $image_name = $prod_name . rand(1000,5243) . '.jpg';
            $imagePath = $this->kernel->getProjectDir() . '/assets/images/' . $image_name;

            if (file_exists($imagePath)) {
                $url = $this->uploaderService->copy($imagePath);
                $product->setImageURL($url);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
