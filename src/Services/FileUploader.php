<?php

namespace App\Services;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private SluggerInterface $slugger;
    private Filesystem $filesystem;
    
    public function __construct(
        Filesystem $articleFileSystem,
        SluggerInterface $slugger
    ) {
        $this->slugger = $slugger;
        $this->filesystem = $articleFileSystem;
    }
    
    public function uploadFile(File $file, ?string $oldFilename = null): string
    {
        $originalFilename = $file instanceof UploadedFile
            ? $file->getClientOriginalName()
            : $file->getFileName()
        ;
        
        $fileName = $this->slugger
            ->slug(pathinfo($originalFilename, PATHINFO_FILENAME))
            ->append('-', uniqid())
            ->append('.', $file->guessExtension())
            ->toString();
        
        $stream = fopen($file->getPathname(), 'r');
        $this->filesystem->writeStream($fileName, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        
        if ($oldFilename && $this->filesystem->fileExists($oldFilename)) {
            $this->filesystem->delete($oldFilename);
        }

        return $fileName;
    }
}