<?php

namespace App\Controller\Api\Articles;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateController extends AbstractController
{
    /**
     * @throws ReflectionException
     */
    #[Route('/api/v1/articles/{id}/update', name: 'api_articles_update')]
    public function index(
        TranslatorInterface $translator,
        Request $request,
        EntityManagerInterface $manager,
        Article $article,
    ): Response {
        
        $badData = [
            'success' => false,
            'message' => [
                'type' => 'danger',
                'text' => $translator->trans('Failed to update article')
            ]
        ];
        $payload = $request->toArray();
        if (!key_exists('fields', $payload)) {
            return $this->json($badData);
        }
        
        foreach ($payload['fields'] as $field) {
            if (!$article->hasField($field['name'])
                || !$article->setField($field['name'], $field['value'])) {
                
                return $this->json($badData);
            }
        }
    
        $manager->persist($article);
        $manager->flush($article);
        
        return $this->json([
            'success' => true,
            'message' => [
                'type' => 'success',
                'text' => $translator->trans('Article successful updated', ['id' => $article->getId()])
            ]
        ]);
    }
}
