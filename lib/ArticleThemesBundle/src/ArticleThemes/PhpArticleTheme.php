<?php

namespace Maxim\ArticleThemesBundle\ArticleThemes;

use Maxim\ArticleThemesBundle\ArticleTheme;

class PhpArticleTheme extends ArticleTheme
{
    
    public function getAllowedParagraphs(): array
    {
        $result = [];
        for ($i = 0; $i < 10; $i++) {
            $result[] = $this->faker->realText();
        }
        return $result;
    }
    
    public function getAllowedTitles(): array
    {
        $result = [];
        for ($i = 0; $i < 10; $i++) {
            $result[] = $this->faker->realText(30);
        }
        return $result;
    }
    
    public function getImagesSubdirectory(): string
    {
        return 'php';
    }
    
    public function getCode(): string
    {
        return 'php';
    }
    
    public function getLabel(): string
    {
        return 'Про PHP';
    }
    
    public function getAllowedArticleTitles(): array
    {
        $result = [];
        for ($i = 0; $i < 5; $i++) {
            $result[] = $this->faker->realText(30);
        }
        return $result;
    }
}