<?php

namespace Maxim\ArticleThemesBundle\ArticleThemes;

use Maxim\ArticleThemesBundle\ArticleTheme;

class FoodArticleTheme extends ArticleTheme
{
    
    public function getAllowedParagraphs(): array
    {
        return [
            'Sour milk soup is just not the same without pepper and large fresh pumpkin seeds.',
            'Aged chicken lard can be made cored by decorating with ketchup.',
            'Squeezed, packaged pudding is best tossed with fresh iced tea.',
            'Combine eggs, rice and strawberries. toss with fluffy parsley and serve cooked with truffels. Enjoy!',
            'Cook ground cracker crumps in a frying pan with cream for about an hour to upgrade their bitterness.',
            'For a melted large salad, add some mint sauce and basil.',
            'All children like peelled chicories in sweet chili sauce and marmalade.',
            'To the chilled cucumber add carrots, lobster, sour milk and sweet oysters.',
            'For a large cold paste, add some soy sauce and garlic.',
            'What’s the secret to divided and sticky avocado? Always use quartered vegemite.',
        ];
    }
    
    public function getAllowedTitles(): array
    {
        return [
            'Soy sauce. ',
            'Ricotta soup',
            'Ramen with warm cinnamon',
            'Radish sprouts plastic bag.',
            'Olive oil in frying pan.',
        ];
    }
    
    public function getImagesSubdirectory(): string
    {
        return 'food';
    }
    
    public function getLabel(): string
    {
        return 'Про еду';
    }
    
    public function getCode(): string
    {
        return 'food';
    }
    
    public function getAllowedArticleTitles(): array
    {
        return [
            'Did you try breaking cake jumbled with sweet chili sauce?',
            'Breaking cooking news.',
            'How placed the onion in a basin?',
            'What people think about wasabi?',
            'Food - let\'s talk about it.'
        ];
    }
}