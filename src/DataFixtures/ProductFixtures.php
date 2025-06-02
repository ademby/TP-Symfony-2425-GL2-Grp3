<?php

namespace App\DataFixtures;

use App\Entity\Category;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\UploaderService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\Product;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private $kernel;
    private $uploaderService;

    public function __construct(KernelInterface $kernel, UploaderService $uploaderService)

    {
        $this->kernel = $kernel;
        $this->uploaderService = $uploaderService;
    }

    public function load(ObjectManager $manager): void
    {

        $this->loadCategories($manager);

        $this->loadProductsOfCategory($manager, "laptop");
        $this->loadProductsOfCategory($manager, "computer");
        $this->loadProductsOfCategory($manager, "monitor");
        $this->loadProductNoCategory ($manager, "mouse");
        $this->loadProductNoCategory ($manager, "keyboard");


    }
    private function generateProductName(string $category): string
    {
        $brands = [
            'computer' => ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus'],
            'laptop' => ['MacBook', 'ThinkPad', 'ZenBook', 'XPS', 'Surface'],
            'monitor' => ['UltraSharp', 'ProDisplay', 'ViewFinity', 'Spectre', 'OLED Pro']
        ];

        $models = ['Pro', 'Max', 'Air', '360', 'Touch', 'Elite'];

        return sprintf('%s %s %d',
            $brands[$category][array_rand($brands[$category])],
            $models[array_rand($models)],
            rand(1000, 9999)
        );
    }

    private function generateDescription(string $title): string
    {
        $features = [
            "Ultra-fast performance",
            "Energy efficient design",
            "Crisp, vibrant display",
            "Ergonomic construction",
            "Premium build quality"
        ];

        shuffle($features);
        return sprintf(
            "The %s delivers %s. %s. Perfect for %s.",
            $title,
            $features[0],
            $features[1],
            ['professionals', 'gamers', 'creatives', 'everyday use'][rand(0, 3)]
        );
    }

    }

    public function loadProductsOfCategory(ObjectManager $manager, string $cat_name): void
    {
        $faker = \Faker\Factory::create();
        $price_min = 100;
        $price_max = 9000;

        for ($i = 0; $i < 6; $i++) {
            $product = new Product();
            $title = $this->generateProductName($cat_name);
            $product->setTitle($title);
            $product->setDescription($this->generateDescription($title));
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


            $image_name = $cat_name . '_' . rand(1, 6) . '.png';
            $imagePath = $this->kernel->getProjectDir() . '/dummy-data/' . $cat_name . '/' . $image_name;


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


            $image_name = $prod_name . '.png';
            $imagePath = $this->kernel->getProjectDir() . '/dummy-data/' . $image_name;


            if (file_exists($imagePath)) {
                $url = $this->uploaderService->copy($imagePath);
                $product->setImageURL($url);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [CleanFixtures::class, CategoryFixtures::class];
    }
}
