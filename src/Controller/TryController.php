<?php

namespace App\Controller;

use App\Entity\FlashMessage;
use App\Events\VisitPublicPageEvent;
use App\Form\CreateDemoArticleFormType;
use App\Form\Model\DemoArticle;
use App\Services\DemoArticleGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TryController extends AbstractController
{
    private const DEMO_ARTICLE_SK = 'demoArticle';
    
    #[Route('/try', name: 'app_try')]
    public function index(
        Request $request,
        DemoArticleGenerator $articleGenerator,
        TranslatorInterface $translator
    ): Response {
    
        if ($this->getUser()) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('You are already logged in')
            ));
    
            return $this->redirectToRoute('app_dashboard');
        }
        
        $defaultContent = [
            'При генерации контента статьи, вы можете наполнить его нужными словами для вашего бизнеса. Столько сколько нужно. Хоть все ими заполоните!',
            'Надоели стандартные красивые изображения. Прикрепляйте к вашим статьям свою уникальные фотографии. Смазанные, с пальцем на пол фотографии, с кривым лицом. Все пойдет - вы здесь главный!',
            'Придумайте и настройте свою собственную интеграцию с сервисом. Нужно ответить на комментарий в соц.сети - получите его по API. Нужно написать новую статью по программированию - получите ее по API. Хотите вкусно покушать - сходите за едой, а статью пускай за вас напишет API!',
        ];
        
        $session = $request->getSession();
        $formOptions = [
            'disabled' => false,
        ];
        if ($session->has(self::DEMO_ARTICLE_SK)) {
            $article = $session->get(self::DEMO_ARTICLE_SK);
            $formOptions['disabled'] = true;
        } else {
            $article = new DemoArticle();
        }
        
        $form = $this->createForm(CreateDemoArticleFormType::class, $article, $formOptions);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set(
                self::DEMO_ARTICLE_SK,
                $article
            );
            
            return $this->redirectToRoute('app_try');
        }
    
        if ($session->has(self::DEMO_ARTICLE_SK)) {
            $article->setContent(
                $articleGenerator->generate($article, $defaultContent)
            );
        }
        
        return $this->render('try.html.twig', [
            'demoArticleForm' => $form->createView(),
            'article' => $session->has(self::DEMO_ARTICLE_SK) ? $article : null,
            'demoArticleCreated' => $session->has(self::DEMO_ARTICLE_SK),
            'defaultContent' => $defaultContent,
        ]);
    }
}
