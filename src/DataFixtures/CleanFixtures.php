<?php

namespace App\DataFixtures;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\UploaderService;
use Symfony\Component\Console\Output\OutputInterface;

class CleanFixtures extends Fixture
{
    private UploaderService $uploaderService;
    public function __construct(UploaderService $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }

    public function load(ObjectManager $manager): void
    {
        
        $this->uploaderService->clean();
        
    }
}
