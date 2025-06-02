<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderService
{
    private string $uploadDir;
    private Filesystem $filesystem;
    private SluggerInterface $slugger;

    public function __construct(string $uploadDir, SluggerInterface $slugger)
    {
        $this->uploadDir = rtrim($uploadDir, '/');
        $this->filesystem = new Filesystem();
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $file->move($this->uploadDir, $newFilename);

        return 'uploads/'.$newFilename;
    }

    public function copy(string $sourcePath): string
    {
        if (!$this->filesystem->exists($sourcePath)) {
            throw new \InvalidArgumentException("Source file does not exist: $sourcePath");
        }

        $originalFilename = pathinfo($sourcePath, PATHINFO_FILENAME);
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;

        $destination = $this->uploadDir . '/' . $newFilename;

        $this->filesystem->copy($sourcePath, $destination);

        return 'uploads/'.$newFilename;
    }

    public function clean(): void
    {
        if ($this->filesystem->exists($this->uploadDir)) {
            $this->filesystem->remove($this->uploadDir); // Remove the directory and all its contents
        }

        // Recreate the directory with proper permissions
        $this->filesystem->mkdir($this->uploadDir, 0777); // or adjust permissions as necessary
    }
}


// Usage:
// use App\Service\UploaderService;
// if ($form->isSubmitted() && $form->isValid()) {
//     $file = $form->get('image')->getData();
//
//     if ($file) {
//         $imageUrl = $uploaderService->upload($file);
//         $entity->setImageUrl($imageUrl);
//     }
// }
// ---------------------------------------------
// use App\Service\UploaderService;
//
// $imagePath = __DIR__.'/images/sample.jpg';
// $imageUrl = $uploaderService->copy($imagePath);
// $entity->setImageUrl($imageUrl);
