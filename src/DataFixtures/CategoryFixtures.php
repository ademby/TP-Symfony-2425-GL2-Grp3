<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\UploaderService;

class CategoryFixtures extends Fixture
{
    public function __construct(
        private KernelInterface $kernel,
        private UploaderService $uploaderService
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->seed(0);

        // We decided not to use Faker for categories for the moment

        $categories = ["Computer", "Laptop", "Monitor"];
        foreach ($categories as $catName) {
            $category = new Category();
            $category->setName($catName);
            $imageName = "cat_" . strtolower($catName) . '.png';
            $imagePath = $this->kernel->getProjectDir() . "/dummy-data/" . strtolower($catName) . '/' . $imageName;

            $url = $this->uploaderService->copy($imagePath);
            $category->setImageURL($url);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
