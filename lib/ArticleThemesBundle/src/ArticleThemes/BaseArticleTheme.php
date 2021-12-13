<?php

namespace Maxim\ArticleThemesBundle\ArticleThemes;

use Maxim\ArticleThemesBundle\ArticleTheme;

class BaseArticleTheme extends ArticleTheme
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
        return 'base';
    }
    
    public function getCode(): string
    {
        return '-';
    }
    
    public function getLabel(): string
    {
        return '-';
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