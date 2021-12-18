<?php

namespace App\Controller\Api\Articles;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleCreateType;
use App\Services\ArticleGenerator;
use App\Services\FileUploader;
use App\Services\FormHelper;
use App\Services\KeywordFormatter;
use App\Services\SubscriptionChecker;
use App\Services\WordFormatter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class CreateController extends AbstractController
{
    const HEADERS = [
        'Content-type' => 'application/json; charset=utf-8'
    ];
    
    #[Route('/api/v1/articles/create', name: 'api_articles_create', methods: ['POST'])]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        RouterInterface $router,
        EntityManagerInterface $manager,
        FileUploader $articleFileUploader,
        ArticleGenerator $articleGenerator,
        SubscriptionChecker $subscriptionChecker,
        KeywordFormatter $keywordFormatter,
        WordFormatter $wordFormatter,
        FormHelper $formHelper
    ): JsonResponse {
        
        /**
         * @var User $user
         */
        $user = $this->getUser();
        
        if (!$subscriptionChecker->canCreateArticle($user)) {
            return $this->json(
                [
                    'success' => false,
                    'errors' => [
                        $translator->trans('Article creation limit exceeded', [
                            'url' => $router->generate('app_dashboard_subscription')
                        ])
                    ],
                    'data' => [],
                ],
                Response::HTTP_OK,
                self::HEADERS
            );
        }
        
        $article = new Article();
        $article->setAuthor($user);
        $keywordFormatter->format($article);
        
        $form = $this->createForm(
            ArticleCreateType::class,
            $article,
            [
                'csrf_protection' => false
            ]
        );
        
        $payload = $this->formatPayload(
            $request->toArray(),
            $keywordFormatter,
            $wordFormatter
        );
        $request->request->set($form->getName(), $payload);
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
            
            
            return $this->json(
                [
                    'success' => true,
                    'errors' => [],
                    'data' => [
                        'title' => $article->getTitle(),
                        'description' => $articleGenerator->generateDescription($article),
                        'content' => htmlspecialchars_decode($article->getContent()),
                    ],
                ],
                Response::HTTP_OK,
                self::HEADERS
            );
        }
        
        return $this->json(
            [
                'success' => false,
                'errors' => $formHelper->getFormErrors($form),
                'data' => [],
            ],
            Response::HTTP_OK,
            self::HEADERS
        );
    }
    
    private function formatPayload(
        array $payload,
        KeywordFormatter $keywordFormatter,
        WordFormatter $wordFormatter,
    ): array {
        
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $payload['keyword'] = $keywordFormatter->formatForApi($payload['keyword'], $user->getSubscription());
        $payload['words'] = $wordFormatter->formatForApi($payload['words'], $user->getSubscription());
        if ($payload['size'] && is_numeric($payload['size'])) {
            $payload['sizeFrom'] = (int)$payload['size'];
            unset($payload['size']);
        }
        if (!($payload['images'] &&
            ($user->getSubscription()->isValid()
                && !$user->getSubscription()->isFree()
            )
        )) {
            unset($payload['images']);
        }
        
        return $payload;
    }
}