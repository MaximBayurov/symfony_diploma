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
        return [
            $this->pasteWords->paste($defaultContent[0], $article->getWords()),
            $this->pasteWords->paste($defaultContent[1], $article->getWords()),
            $this->pasteWords->paste($defaultContent[1], $article->getWords()),
            $this->pasteWords->paste($defaultContent[2], $article->getWords()),
        ];
    }
}