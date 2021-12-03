<?php

namespace App\Services;

use App\Form\Model\DemoArticle;

class DemoArticleGenerator
{
    public function __construct(
        private PasteWords $pasteWords
    ) {
    }
    
    public function generate(DemoArticle $article, array $defaultContent): array
    {
        array_walk($defaultContent, function (&$item) use ($article) {
            $item = $this->pasteWords->paste($item, $article->getWords());
        });
        return $defaultContent;
    }
}