<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class KeywordExtension extends AbstractExtension
{
    private static $keywordMap = [
        0 => 'nominative',
        1 => 'genitive',
        2 => 'dative',
        3 => 'accusative',
        4 => 'instrumental',
        5 => 'prepositional',
        6 => 'plural',
    ];
    
    public function getFilters(): array
    {
        return [
            new TwigFilter('morph', [$this, 'morph']),
        ];
    }

    public function morph($value, $index)
    {
        if (!key_exists($index, self::$keywordMap)) {
            $index = 0;
        }
        $key = self::$keywordMap[$index];
        return $value[$key];
    }
}
