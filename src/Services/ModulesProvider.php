<?php

namespace App\Services;

use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;

class ModulesProvider
{
    public function __construct(
        private Filesystem $modulesFileSystem
    ) {
    }
    
    public function getAllowedModules(): array
    {
        return $this->modulesFileSystem->listContents('/')->filter(function ($file) {
            return !$file->isDir();
        })->toArray();
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
            if (!is_array($keys)) {
                $keys = [$keys];
            }
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
            if (!is_array($keys)) {
                $keys = [$keys];
            }
            $result = [];
            foreach ($keys as $key) {
                $result[] = $this->loadModule($modules[$key]);
            }
        }
        
        return $result;
    }
    
    protected function loadModule(FileAttributes $module): string
    {
        return $this->modulesFileSystem->read($module->path());
    }
    
}