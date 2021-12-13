<?php

namespace App\Controller\Dashboard;

use App\Entity\Subscription;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/dashboard/subscription', name: 'app_dashboard_subscription')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
                'text' => $levelType['TEXT'],
                'price' => $levelType['PRICE'],
                'sort' => $levelType['SORT'],
                'value' => $levelType['VALUE'],
                'isCurrent' => $currentLevel['VALUE'] === $levelType['VALUE'],
                'showButton' => $currentLevel['VALUE'] <= $levelType['VALUE'],
            ];
        }
        
        usort($typesFormatted, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });
        
        return $typesFormatted;
    }
}
