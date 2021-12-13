<?php

namespace Maxim\ArticleThemesBundle;

class BaseArticleThemesProvider extends ArticleThemesProvider
{
    
    public function __construct(
        private array $allowedThemeClasses
    ) {
    }
    
    /**
     * @return ArticleTheme[]
     */
    protected function getAllowedThemes(): array
    {
        $result = [];
        foreach ($this->allowedThemeClasses as $theme) {
            if (!class_exists($theme)) {
                continue;
            }
            $theme = new $theme();
            if ($theme instanceof ArticleTheme) {
                $result[] = $theme;
            }
        }
        return $result;
    }
}