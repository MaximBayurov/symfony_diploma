<?php

namespace App\Controller\Dashboard\Article;

use App\Entity\Article;
use App\Entity\FlashMessage;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DetailController extends AbstractController
{
    #[Route('/dashboard/article/{id}/detail/', name: 'app_dashboard_article_detail')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Article $article,
        TranslatorInterface $translator,
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (
            $user->getId() !== $article->getAuthor()->getId()) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('Only author can view the article')
            ));
            
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('dashboard/article/detail.html.twig', [
            'article' => $article,
        ]);
    }
}
