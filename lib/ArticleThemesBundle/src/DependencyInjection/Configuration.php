<?php

namespace Maxim\ArticleThemesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('maxim_article_themes');
        $rootNode = $treeBuilder->getRootNode();
        
        $rootNode
            ->fixXmlConfig('theme')
                ->children()
                ->arrayNode('themes')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}