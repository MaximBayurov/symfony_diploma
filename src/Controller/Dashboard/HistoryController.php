<?php

namespace App\Controller\Dashboard;

use App\Entity\FlashMessage;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\GeneratorModuleRepository;
use App\Services\ArticleChecker;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryController extends AbstractController
{
    #[Route('/dashboard/history', name: 'app_dashboard_history')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        Request $request,
        TranslatorInterface $translator,
    ): Response {
    
        /**
         * @var User $user
         */
        $user = $this->getUser();
    
        if($articleRepository->hasUserDescription($user->getId()) === false) {
    
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('Update article description help')
            ));
        }
        
        $pagination = $paginator->paginate(
            $articleRepository->getUserLatest($user->getId()),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('dashboard/history.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
