<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
    
    /**
     * @param int $userID
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function lastHourUserArticlesCount(int $userID): int
    {
        $queryBuilder = $this->createQueryBuilder('a');
        
        $result = $queryBuilder
            ->select('COUNT(a.id) as count')
            ->andWhere('a.author = :userID')
            ->setParameter('userID', $userID)
            ->andWhere('a.createdAt >= :lastHour')
            ->setParameter('lastHour', new \DateTime('-1 hour'))
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
        
        return (int)$result['count'];
    }
}