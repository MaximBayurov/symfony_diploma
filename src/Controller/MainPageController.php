<?php

namespace App\Controller;

use App\Entity\FlashMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(
        TranslatorInterface $translator
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('You are already logged in')
            ));
    
            return $this->redirectToRoute('app_dashboard');
        }
        
        return $this->render('index.html.twig', [
        ]);
    }
}
