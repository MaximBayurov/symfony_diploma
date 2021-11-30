<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixtures extends Fixture
{
    protected ObjectManager $manager;
    protected Generator $faker;
    
    private array $referencesIndex = [];
    
    abstract function loadData(): void;
    
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;
        
        $this->loadData();
    }
    
    protected function create(string $className, callable $factory)
    {
        $entity = new $className();
        $factory($entity);
    
        $this->manager->persist($entity);
        return $entity;
    }
    
    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $this->create($className, $factory);
            
            $this->addReference("$className|$i", $entity);
        }
        
        $this->manager->flush();
    }
    
    /**
     * @throws Exception
     */
    protected function getRandomReference($className)
    {
        if (! isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            
            foreach ($this->referenceRepository->getReferences() as $key => $reference) {
                if (str_starts_with($key, $className . '|')) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        
        if (empty($this->referencesIndex[$className])) {
            throw new Exception('Не найдены ссылки на класс: ' . $className);
        }
        
        return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
    }
    
}
