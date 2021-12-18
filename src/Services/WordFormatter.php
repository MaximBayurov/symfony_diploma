<?php

namespace App\Services;

use App\Entity\Subscription;

class WordFormatter
{
    public function formatForApi(?array $word, Subscription $subscription): array
    {
        if (!$subscription->isFree()
            && $subscription->isValid()) {
        
            return $word;
        }
    
        return [
            reset($word)
        ];
    }
}