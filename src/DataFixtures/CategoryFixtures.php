<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\UploaderService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private UploaderService $uploaderService
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->seed(0);
        $categories = ["computer", "laptop", "monitor"];
        foreach ($categories as $catName) {
            $category = new Category();
            $category->setName($catName);
            $imageName = $catName . '.png';
            $imagePath = $this->kernel->getProjectDir() . "/dummy-data/" . $catName . '/' . $imageName;
            $url = $this->uploaderService->copy($imagePath);
            $category->setImageURL($url);
            $manager->persist($category);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CleanFixtures::class];
    }
}
