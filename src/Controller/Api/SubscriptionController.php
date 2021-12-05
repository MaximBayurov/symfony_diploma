<?php

namespace App\Controller\Api;

use App\Entity\FlashMessage;
use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriptionController extends AbstractController
{
    #[Route('/api/v1/subscribe/', name: 'api_subscription', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        SubscriptionRepository $subscriptionRepository,
        TranslatorInterface $translator,
        Mailer $mailer
    ): Response {
        $payload = $request->toArray();
        $level = $payload['level'];
        
        if (empty($level)
            || !Subscription::isValidType($level)
        ) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }
        
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $subscription = $user->getSubscription();
        
        if (!$subscription->isFree()) {
            $subscriptionRepository->deleteByID($subscription->getId());
            $subscription = new Subscription();
            $subscription->setUser($user);
        }
        
        $subscription->setLevel($level);
        $subscription->setExpiresAt(new \DateTimeImmutable('+1 week'));
        
        $entityManager->persist($subscription);
        $entityManager->flush();
        
        $mailer->sendSubscribe($subscription);
        
        $this->addFlash('flash_message', new FlashMessage(
            $translator->trans('level subscribed, up to expiresAt', [
                'level' => $level,
                'expiresAt' => $subscription->getExpiresAt()->format('d.m.Y'),
            ])
        ));
        
        return $this->json([]);
    }
}
