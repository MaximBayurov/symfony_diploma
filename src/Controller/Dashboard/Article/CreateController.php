<?php

namespace App\Controller\Dashboard\Article;

use App\Entity\Article;
use App\Entity\FlashMessage;
use App\Entity\User;
use App\Form\ArticleCreateType;
use App\Repository\ArticleRepository;
use App\Services\ArticleGenerator;
use App\Services\FileUploader;
use App\Services\KeywordFormatter;
use App\Services\SubscriptionChecker;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class CreateController extends AbstractController
{
    const GENERATED_ARTICLE_SK = 'GENERATED_ARTICLE_CONTENT';
    
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ArticleRepository $articleRepository
     * @param RouterInterface $router
     * @param EntityManagerInterface $manager
     * @param FileUploader $articleFileUploader
     * @param ArticleGenerator $articleGenerator
     * @return Response
     * @throws NonUniqueResultException
     * @throws LoaderError
     * @throws SyntaxError
     */
    #[Route('/dashboard/article/create', name: 'app_dashboard_article_create')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        RouterInterface $router,
        EntityManagerInterface $manager,
        FileUploader $articleFileUploader,
        ArticleGenerator $articleGenerator,
        SubscriptionChecker $subscriptionChecker,
        KeywordFormatter $keywordFormatter,
    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        
        $formOptions = [];
        if (!$subscriptionChecker->canCreateArticle($user)) {
            $this->addFlash(
                'flash_message',
                new FlashMessage(
                    $translator->trans('Article creation limit exceeded', [
                        'url' => $router->generate('app_dashboard_subscription')
                    ]),
                    'danger'
                )
            );
            
            $formOptions['disabled'] = true;
        }
        
        $article = new Article();
        $article->setAuthor($user);
        $keywordFormatter->format($article);
        
        $form = $this->createForm(
            ArticleCreateType::class,
            $article,
            $formOptions
        );
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($images = $article->getImages()) {
                foreach ($images as &$image) {
                    $image = $articleFileUploader->uploadFile($image, $image->getPathname());
                }
                unset($image);
                $article->setImages($images);
            }
            
            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());
            
//            $manager->persist($article);
//            $manager->flush($article);
            
            $this->addFlash(
                'flash_message',
                new FlashMessage(
                    $translator->trans('Article successful created')
                )
            );
            
            $request->getSession()->set(
                self::GENERATED_ARTICLE_SK,
                $articleGenerator->generate($article)
            );
            
            return $this->redirectToRoute('app_dashboard_article_create');
        }
        
        return $this->render(
            'dashboard/article/create.html.twig',
            [
                'articleForm' => $form->createView(),
                'generatedArticle' => $this->getGeneratedArticle($request),
            ]
        );
    }
    
    private function getGeneratedArticle(Request $request): ?string
    {
        if ($article = $request->getSession()->get(self::GENERATED_ARTICLE_SK)) {
            $request->getSession()->remove(self::GENERATED_ARTICLE_SK);
            return $article;
        }
        
        return null;
    }
}
