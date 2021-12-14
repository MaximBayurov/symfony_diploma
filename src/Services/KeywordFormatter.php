<?php

namespace App\Services;

use App\Entity\Article;

class KeywordFormatter
{
    
    public function format(Article $article): void
    {
        if (!$article->getAuthor()->getSubscription()->isFree()
            && $article->getAuthor()->getSubscription()->isValid()) {
            
            $default = [
                'nominative' => '',
                'genitive' => '',
                'dative' => '',
                'accusative' => '',
                'instrumental' => '',
                'prepositional' => '',
                'plural' => '',
            ];
        } else {
            $default = [
                'nominative' => '',
            ];
        }
        
        $keywords = !empty($article->getKeyword())
            ? $article->getKeyword()
            : $default;
        
        $nominative = $keywords['nominative'];
        array_walk($keywords, function (&$keyword) use ($nominative) {
            $keyword = empty($keyword) ? $nominative : $keyword;
        });
        $article->setKeyword($keywords);
    }
}