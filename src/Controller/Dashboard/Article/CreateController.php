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
use Maxim\ArticleThemesBundle\Exceptions\ThemeNotProvidedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateController extends AbstractController
{
    const GENERATED_ARTICLE_SK = 'GENERATED_ARTICLE_CONTENT';
    
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param EntityManagerInterface $manager
     * @param FileUploader $articleFileUploader
     * @param ArticleGenerator $articleGenerator
     * @param SubscriptionChecker $subscriptionChecker
     * @param KeywordFormatter $keywordFormatter
     * @param ArticleRepository $articleRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws ThemeNotProvidedException
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
        ArticleRepository $articleRepository,
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
        
        $article = $this->createArticle(
            $request->query->getInt('id'),
            $articleRepository,
            $user
        );
        $keywordFormatter->format($article);
        
        $form = $this->createForm(
            ArticleCreateType::class,
            $article,
            $formOptions
        );
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $article = $form->getData();
            
            if ($images = $article->getImages()) {
                foreach ($images as &$image) {
                    if ($image instanceof UploadedFile) {
                        $image = $articleFileUploader->uploadFile($image, $image->getPathname());
                    }
                }
                unset($image);
                $article->setImages($images);
            }
            
            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());
            
            $content = $articleGenerator->generate($article);
            $article->setContent($content);
            
            $manager->persist($article);
            $manager->flush($article);
            
            $this->addFlash(
                'flash_message',
                new FlashMessage(
                    $translator->trans('Article successful created')
                )
            );
            
            $request->getSession()->set(
                self::GENERATED_ARTICLE_SK,
                $content
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
    
    private function createArticle(int $articleID, ArticleRepository $repository, User $user): Article
    {
        /**
         * @var Article $article
         */
        if ($articleID > 0) {
            $article = $repository->findOneByID($articleID, $user->getId());
            $article = $article
                ? clone $article
                : null;
        }
        
        if (empty($article)) {
            $article = new Article();
            $article->setAuthor($user);
        }
        
        return $article;
    }
}
