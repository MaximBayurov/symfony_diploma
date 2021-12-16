<?php

namespace App\Controller\Dashboard;

use App\Entity\FlashMessage;
use App\Entity\User;
use App\Repository\ArticleRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    #[Route('/dashboard/', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        TranslatorInterface $translator,
        ArticleRepository $repository,
    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ( $user->getSubscription()->isValid()
            && $user->getSubscription()->getExpiresAt() < new DateTimeImmutable('+3 days')) {
            $this->addFlash(
                'flash_message',
                new FlashMessage(
                    $translator->trans('Subscription expires on expiresAt', [
                        'expiresAt' => $user->getSubscription()->getExpiresAt()->format('d.m.Y в H:i:s')
                    ]),
                    'danger'
                )
            );
            
        }
        
        $lastArticle = $repository->getOneUserLatest($user->getId());
        $statistic = [
            'total' => $repository->userArticlesCount($user->getId()),
            'thisMonth' => $repository->userArticlesCount($user->getId(), new DateTime(
                (new DateTime())->format('Y-m-01 00:00:00')
            )),
        ];
        
        return $this->render('dashboard/index.html.twig', [
            'subscription' => $user->getSubscription(),
            'lastArticle' => $lastArticle,
            'statistic' => $statistic
        ]);
    }
}
