<?php

namespace Maxim\ArticleThemesBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ArticleThemesBundleExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.xml');
    
        $configuration = $this->getConfiguration($configs, $container);
    
        $config = $this->processConfiguration($configuration, $configs);
    
        $definition = $container->getDefinition('Maxim\ArticleThemesBundle\BaseArticleThemesProvider');
        $definition->setArgument(0, $config['themes']);
    }
    
    public function getAlias(): string
    {
        return 'maxim_article_themes';
    }
}