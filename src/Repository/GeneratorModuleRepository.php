<?php

namespace App\Repository;

use App\Entity\GeneratorModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeneratorModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneratorModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneratorModule[]    findAll()
 * @method GeneratorModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneratorModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneratorModule::class);
    }
    
    public function getUserLatest(int $userID): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('gm');
    
        return $queryBuilder
            ->andWhere('gm.user = :userID')
            ->setParameter('userID', $userID)
            ->orderBy('gm.createdAt', 'DESC');
    }
    
    public function deleteByID(int $ID): bool
    {
        $rowsAffected = $this->createQueryBuilder('gm')
            ->delete('App:GeneratorModule', 'gm')
            ->where('gm.id = :module_id')
            ->setParameter('module_id', $ID)
            ->getQuery()
            ->getResult()
        ;
        return $rowsAffected > 0;
    }
}
