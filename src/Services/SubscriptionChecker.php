<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SubscriptionChecker
{
    private const ARTICLE_PER_HOUR_LIMIT = 2;
    
    public function __construct(
        private ArticleRepository $articleRepository,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }
    
    /**
     * @throws NonUniqueResultException
     */
    public function canCreateArticle(User $user): bool
    {
        if ($user->getSubscription()->isValid() && $this->authorizationChecker->isGranted('SUBSCRIPTION_PRO')) {
            return true;
        }
        $articlesCount = $this->articleRepository->userArticlesCount($user->getId(), new DateTime('-1 hour'));
        return $articlesCount < self::ARTICLE_PER_HOUR_LIMIT;
    }
}