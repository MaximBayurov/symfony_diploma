<?php

namespace Maxim\ArticleThemesBundle;

use Faker\Factory;
use Faker\Generator;
use League\Flysystem\Filesystem;

abstract class ArticleTheme
{
    protected Generator $faker;
    
    public function __construct()
    {
        $this->faker = Factory::create();
    }
    
    abstract public function getAllowedParagraphs(): array;
    
    abstract public function getAllowedTitles(): array;
    
    abstract public function getImagesSubdirectory(): string;
    
    abstract public function getAllowedArticleTitles(): array;
    
    abstract public function getCode(): string;
    
    abstract public function getLabel(): string;
    
    public function getAllowedModules(): array
    {
        return [
            'text',
            'media',
            'media_left',
            'media_left_one_paragraph',
            'media_right_one_paragraph',
            'subtitle_contents',
            'title_contents',
            'two_column_text',
        ];
    }
    
    public function getModules(int $min, int $max, array $additionalModules = []): array
    {
        $modules = $this->getAllowedModules();
        if ($additionalModules) {
            array_walk($modules, function (&$module) {
                $module = $this->loadModule($module);
            });
            unset($module);
            $modules = array_merge(
                $modules,
                $additionalModules
            );
            
            $count = random_int($min, $max);
            while (count($modules) < $count) {
                $modules = array_merge($modules, $modules);
            }
            
            $keys = array_rand($modules, $count);
            $result = [];
            foreach ($keys as $key) {
                $result[] = $modules[$key];
            }
        } else {
            $count = random_int($min, $max);
            while (count($modules) < $count) {
                $modules = array_merge($modules, $modules);
            }
            
            $keys = array_rand($modules, $count);
            $result = [];
            foreach ($keys as $key) {
                $result[] = $this->loadModule($modules[$key]);
            }
        }
        
        return $result;
    }
    
    protected function loadModule(string $module): string
    {
        return file_get_contents('../modules/' . $module . '.twig');
    }
    
    public function getParagraphs(int $count): array
    {
        $paragraphs = $this->getAllowedParagraphs();
        while (count($paragraphs) < $count) {
            $paragraphs = array_merge($paragraphs, $paragraphs);
        }
        $keys = array_rand($paragraphs, $count);
        $result = [];
        foreach ($keys as $key) {
            $result[] = $paragraphs[$key];
        }
        
        return $result;
    }
    
    public function getTitle(): string
    {
        $titles = $this->getAllowedTitles();
        $key = array_rand($titles);
        
        return $titles[$key];
    }
    
    public function getArticleTitle(): string
    {
        $titles = $this->getAllowedArticleTitles();
        $key = array_rand($titles);
        
        return $titles[$key];
    }
    
    public function getImage(Filesystem $filesystem, string $filesystemPath): string
    {
        $images = $this->getAllowedImages($filesystem, $filesystemPath);
        $key = array_rand($images);
        
        return $images[$key];
    }
    
    public function getAllowedImages(Filesystem $filesystem, string $filesystemPath): array
    {
        $subdirectoryName = $this->getImagesSubdirectory();
        $subdirectory = $filesystem->listContents('/')->filter(function ($file) use ($subdirectoryName) {
            return $file->isDir() && $file->path() == $subdirectoryName;
        })->toArray();
        
        $images = [];
        if ($subdirectory = reset($subdirectory)) {
            $images = $filesystem->listContents($subdirectory->path())->filter(function ($file) {
                return !$file->isDir();
            })->toArray();
            array_walk($images, function (&$image) use ($filesystemPath) {
                $image = $filesystemPath . '/' . $image->path();
            });
        }
        
        return $images;
    }
}