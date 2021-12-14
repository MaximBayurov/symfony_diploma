<?php

namespace App\Twig;

use App\Services\ArticleGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArticleDescriptionGeneratorExtension extends AbstractExtension
{
    public function __construct(
        private ArticleGenerator $articleGenerator
    ) {
    }
    
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('generate_for_empty', [$this, 'generateIfEmpty']),
        ];
    }

    public function generateIfEmpty($value, $article)
    {
        if (empty($value)) {
            return $this->articleGenerator->generateDescription($article);
        }
        return $value;
    }
}
