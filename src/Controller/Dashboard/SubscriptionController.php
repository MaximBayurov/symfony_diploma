<?php

namespace App\Controller\Dashboard;

use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/dashboard/subscription', name: 'app_dashboard_subscription')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userSubscription = $user->getSubscription();
        
        return $this->render('dashboard/subscription.html.twig', [
            'subscriptionTypes' => $this->getSubscriptionTypes($userSubscription)
        ]);
    }
    
    private function getSubscriptionTypes(Subscription $current): array
    {
        $currentLevel = $current->getLevel();
        $typesFormatted = [];
        foreach (Subscription::LEVEL_TYPES as $levelType) {
            $typesFormatted[] = [
                'value' => $levelType['VALUE'],
                'price' => $levelType['PRICE'],
                'sort' => $levelType['SORT'],
                'isCurrent' => $currentLevel['VALUE'] === $levelType['VALUE'],
                'showButton' => $currentLevel['SORT'] <= $levelType['SORT'],
            ];
        }
        
        return $typesFormatted;
    }
}
