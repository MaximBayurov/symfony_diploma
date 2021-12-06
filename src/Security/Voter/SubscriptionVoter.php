<?php

namespace App\Security\Voter;

use App\Entity\Subscription;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['SUBSCRIPTION_FREE', 'SUBSCRIPTION_PLUS', 'SUBSCRIPTION_PRO']);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
    
        /**
         * @var Subscription $subscription
         */
        $subscription = $user->getSubscription();
        if ($subscription->isValid() === false) {
            return false;
        }
        
        switch ($attribute) {
            case 'SUBSCRIPTION_PRO':
                return $subscription->getLevel()['VALUE'] >= 2;
            case 'SUBSCRIPTION_PLUS':
                return $subscription->getLevel()['VALUE'] >= 1;
            case 'SUBSCRIPTION_FREE':
                return $subscription->getLevel()['VALUE'] >= 0;
        }

        return false;
    }
}
