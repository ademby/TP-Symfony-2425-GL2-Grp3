<?php

namespace App\DataFixtures;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CleanFixtures extends Fixture
{
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function load(ObjectManager $manager): void
    {
        $filesystem = new Filesystem();

        // Clean the upload directory
        if ($filesystem->exists($this->uploadDir)) {
            $filesystem->remove($this->uploadDir); // Remove the directory and its contents
        }

        // Recreate the upload directory
        $filesystem->mkdir($this->uploadDir, 0777); // Adjust the permissions as necessary
    }
}
