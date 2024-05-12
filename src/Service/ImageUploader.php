<?php
namespace App\Service;

use App\Entity\Figure;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newfileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $newfileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
       
        return $newfileName;
    }
    public function uploadImages(Figure $figure): void
    {
        foreach ($figure->getImages() as $image) {
            if ($image->getFile() !== null) {
                $fileName = $this->upload($image->getFile());
                $image->setPath($fileName);
                $image->setImageName($fileName);
            } elseif ($image->getPath() === null && $image->getFile() === null) {
                $figure->removeImage($image);
            }
        }
    }
    

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}