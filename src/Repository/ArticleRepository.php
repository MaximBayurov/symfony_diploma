<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
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
     * @param DateTime $from
     * @return int
     * @throws NonUniqueResultException
     */
    public function userArticlesCount(int $userID, DateTime $from): int
    {
        $queryBuilder = $this->createQueryBuilder('a');
        
        $result = $queryBuilder
            ->select('COUNT(a.id) as count')
            ->andWhere('a.author = :userID')
            ->setParameter('userID', $userID)
            ->andWhere('a.createdAt >= :from')
            ->setParameter('from', $from)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
        
        return (int)$result['count'];
    }
    
    public function getUserLatest(int $userID): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a');
    
        return $queryBuilder
            ->andWhere('a.author = :userID')
            ->setParameter('userID', $userID)
            ->orderBy('a.createdAt', 'DESC');
    }
    
    public function hasUserDescription(int $userID)
    {
    
        $queryBuilder = $this->createQueryBuilder('a');
    
        $result = $queryBuilder
            ->select('COUNT(a.id) as count')
            ->andWhere('a.author = :userID')
            ->setParameter('userID', $userID)
            ->andWhere('a.description IS NOT NULL')
            ->getQuery()
            ->getOneOrNullResult();
        
        return $result['count'] > 0;
    }
    
    public function findOneByID(int $articleID, int $userID): ?Article
    {
        $queryBuilder = $this->createQueryBuilder('a');
    
        return $queryBuilder
            ->andWhere('a.id = :articleID')
            ->setParameter('articleID', $articleID)
            ->andWhere('a.author = :userID')
            ->setParameter('userID', $userID)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
