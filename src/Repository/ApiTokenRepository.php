<?php

namespace App\Repository;

use App\Entity\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiToken[]    findAll()
 * @method ApiToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }
    
    public function deleteByID(int $tokenID): bool
    {
        $rowsAffected = $this->createQueryBuilder('at')
            ->delete('App:ApiToken', 'at')
            ->where('at.id = :token_id')
            ->setParameter('token_id', $tokenID)
            ->getQuery()
            ->getResult()
        ;
        return $rowsAffected > 0;
    }
}