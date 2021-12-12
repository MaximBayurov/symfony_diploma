<?php

namespace Maxim\ArticleThemesBundle;

use Maxim\ArticleThemesBundle\DependencyInjection\ArticleThemesBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArticleThemesBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new ArticleThemesBundleExtension();
        }
        
        return $this->extension;
    }
}