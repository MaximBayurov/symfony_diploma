<?php

namespace App\Services;

class PasteWords
{
    public function paste(string $text, string $word, int $wordsCount = 1): string
    {
        if (empty($word) && $wordsCount < 1) {
            return $text;
        }
        
        $tokens = [];
        preg_match_all('/([^ ]*) ([^ ]*)/', $text, $tokens);
        
        $tokens = reset($tokens);
        $emptyPlacesCount = count($tokens);
        
        while ($wordsCount > 0) {
            $insertInto = random_int(0, $emptyPlacesCount - 1);
            $tokens[$insertInto] = $word . ' ' . strtolower($tokens[$insertInto]);
            $wordsCount--;
        }
        
        return implode(" ", $tokens);
    }
}