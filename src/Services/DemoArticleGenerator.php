<?php

namespace App\Services;

use App\Form\Model\DemoArticle;
use Symfony\Component\HttpFoundation\Request;

class DemoArticleGenerator
{
    private Request $request;
    
    public function __construct(
        private PasteWords $pasteWords,
    ) {
    }
    
    public function generate(DemoArticle $article, array $defaultContent): array
    {
        array_walk($defaultContent, function (&$item) use ($article) {
            $item = $this->pasteWords->paste($item, $article->getWords());
        });
        return $defaultContent;
    }
    
    public function getArticle(): DemoArticle
    {
        $demoArticleCreated = $this->request->cookies->has(
            $this->getDemoArticleCookieName()
        );
        if ($demoArticleCreated) {
            $article = unserialize($this->request->cookies->get(
                $this->getDemoArticleCookieName()
            ));
        } else {
            $article = new DemoArticle();
        }
        return $article;
    }
    
    public function isDemoArticleCreated(): bool
    {
        return $this->request->cookies->has(
            $this->getDemoArticleCookieName()
        );
    }
    
    public function generateArticleContent(DemoArticle $article, $defaultContent, $defaultTitle): void
    {
        if ($this->isDemoArticleCreated()) {
            $article->setContent(
                $this->generate(
                    $article,
                    $defaultContent
                )
            );
        } else {
            $article->setTitle($defaultTitle);
            $article->setContent($defaultContent);
        }
    }
    
    public function getDemoArticleCookieName(): string
    {
        return 'DEMO_ARTICLE_COOKIE';
    }
    
    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}