<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TryController extends AbstractController
{
    #[Route('/try', name: 'app_try')]
    public function index(): Response
    {
        return $this->render('try.html.twig', [
        ]);
    }
}
