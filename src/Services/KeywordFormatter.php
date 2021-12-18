<?php

namespace App\Services;

use App\Entity\Article;
use App\Entity\Subscription;

class KeywordFormatter
{
    
    public function format(Article $article): void
    {
        $default = $this->getDefault($article->getAuthor()->getSubscription());
        
        $keywords = !empty($article->getKeyword())
            ? $article->getKeyword()
            : $default;
        
        $nominative = $keywords['nominative'];
        array_walk($keywords, function (&$keyword) use ($nominative) {
            $keyword = empty($keyword) ? $nominative : $keyword;
        });
        $article->setKeyword($keywords);
    }
    
    public function formatForApi(null|string|array $keyword, Subscription $subscription): array
    {
        if (empty($keyword)) return $this->getDefault($subscription);
        
        $keyword = is_array($keyword) ? $keyword : [$keyword];
        
        if (!$subscription->isFree()
            && $subscription->isValid()) {
            
            return [
                'nominative' => $keyword[0],
                'genitive' => $keyword[1] ?? $keyword[0],
                'dative' => $keyword[2] ?? $keyword[0],
                'accusative' => $keyword[3] ?? $keyword[0],
                'instrumental' => $keyword[4] ?? $keyword[0],
                'prepositional' => $keyword[5] ?? $keyword[0],
                'plural' => $keyword[6] ?? $keyword[0],
            ];
        }
        
        return [
            'nominative' => $keyword[0],
        ];
    }
    
    private function getDefault(Subscription $subscription): array
    {
        if (!$subscription->isFree()
            && $subscription->isValid()) {
        
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
        return $default;
    }
}