<?php

namespace App\Controller;

use App\Entity\FlashMessage;
use App\Form\CreateDemoArticleFormType;
use App\Form\Model\DemoArticle;
use App\Services\DemoArticleGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TryController extends AbstractController
{
    private const DEMO_ARTICLE_COOKIE = 'DEMO_ARTICLE_COOKIE';
    private const DEFAULT_CONTENT = [
        'При генерации контента статьи, вы можете наполнить его нужными словами для вашего бизнеса. Столько сколько нужно. Хоть все ими заполоните!',
        'Надоели стандартные красивые изображения. Прикрепляйте к вашим статьям свою уникальные фотографии. Смазанные, с пальцем на пол фотографии, с кривым лицом. Все пойдет - вы здесь главный!',
        'Надоели стандартные красивые изображения. Прикрепляйте к вашим статьям свою уникальные фотографии. Смазанные, с пальцем на пол фотографии, с кривым лицом. Все пойдет - вы здесь главный!',
        'Придумайте и настройте свою собственную интеграцию с сервисом. Нужно ответить на комментарий в соц.сети - получите его по API. Нужно написать новую статью по программированию - получите ее по API. Хотите вкусно покушать - сходите за едой, а статью пускай за вас напишет API!',
    ];
    const DEFAULT_TITLE = 'Тестовая статья';
    
    #[Route('/try', name: 'app_try')]
    public function index(
        Request $request,
        DemoArticleGenerator $articleGenerator,
        TranslatorInterface $translator,
        RouterInterface $router
    ): Response {
    
        if ($this->getUser()) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('You are already logged in')
            ));
    
            return $this->redirectToRoute('app_dashboard');
        }
        
        $demoArticleCreated = $request->cookies->has(self::DEMO_ARTICLE_COOKIE);
        $formOptions = [
            'disabled' => $demoArticleCreated,
        ];
        if ($demoArticleCreated) {
            $article = unserialize($request->cookies->get(self::DEMO_ARTICLE_COOKIE));
        } else {
            $article = new DemoArticle();
            $article->setContent(self::DEFAULT_CONTENT);
        }
        
        $form = $this->createForm(CreateDemoArticleFormType::class, $article, $formOptions);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $path = $router->generate('app_try');
            $redirectResponse = new RedirectResponse(
                $path,
                Response::HTTP_FOUND
            );
            $redirectResponse->headers->setCookie(new Cookie(
                self::DEMO_ARTICLE_COOKIE,
                serialize($article),
                new \DateTime('+100 years'),
                $path
            ));
            
            return $redirectResponse;
        }
    
        if ($demoArticleCreated) {
            $article->setContent(
                $articleGenerator->generate($article, self::DEFAULT_CONTENT)
            );
        } else {
            $article->setTitle(self::DEFAULT_TITLE);
        }
        
        return $this->render(
            'try.html.twig',
            [
                'demoArticleForm' => $form->createView(),
                'article' => $article,
                'demoArticleCreated' => $demoArticleCreated,
            ]
        );
    }
}
