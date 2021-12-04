<?php

namespace App\Controller\Api;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/api/v1/token/create', name: 'api_token_create', methods: ['POST'])]
    public function index(
        EntityManagerInterface $entityManager,
        ApiTokenRepository $apiTokenRepository
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if(!$user) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }
    
        if ($currentApiToken = $user->getApiToken()) {
            $apiTokenRepository->deleteByID($currentApiToken->getId());
        }
        $apiToken = new ApiToken($user);
    
        $entityManager->persist($apiToken);
        $entityManager->flush();
        
        return $this->json([
            'newToken' => $apiToken->getToken(),
        ]);
    }
}
