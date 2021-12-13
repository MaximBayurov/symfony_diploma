<?php

namespace Maxim\ArticleThemesBundle;

use Maxim\ArticleThemesBundle\Exceptions\ThemeNotProvidedException;

abstract class ArticleThemesProvider
{
    /**
     * @var ArticleTheme[]
     */
    protected static array $themes = [];
    
    /**
     * @return ArticleTheme[]
     */
    abstract protected function getAllowedThemes(): array;
    
    /**
     * @return ArticleTheme[]
     */
    public function getThemes(): array
    {
        if (empty(static::$themes)) {
            static::$themes = $this->getAllowedThemes();
        }
        
        return static::$themes;
    }
    
    /**
     * @return array
     */
    public function getThemesList(): array
    {
        $themes = $this->getThemes();
        
        $result = [];
        foreach ($themes as $theme) {
            $result[$theme->getLabel()] = $theme->getCode();
        }
        return $result;
    }
    
    /**
     * @throws ThemeNotProvidedException
     */
    public function getThemeByCode(string $code): ArticleTheme
    {
        $themes = $this->getThemes();
        
        foreach ($themes as $theme) {
            if ($theme->getCode() === $code) {
                return $theme;
            }
        }
        
        throw new ThemeNotProvidedException();
    }
    
}