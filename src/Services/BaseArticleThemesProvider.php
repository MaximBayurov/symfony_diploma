<?php

namespace App\Services;

use League\Flysystem\Filesystem;
use Maxim\ArticleThemesBundle\ArticleTheme;
use Maxim\ArticleThemesBundle\ArticleThemes\BaseArticleTheme;
use Maxim\ArticleThemesBundle\ArticleThemes\FoodArticleTheme;
use Maxim\ArticleThemesBundle\ArticleThemes\PhpArticleTheme;
use Maxim\ArticleThemesBundle\ArticleThemesProvider;

class BaseArticleThemesProvider extends ArticleThemesProvider
{
    private Filesystem $fileSystem;
    private string $fileSystemPath;
    
    public function __construct(
        Filesystem $imgFileSystem,
        string $imgFileSystemPath
    )
    {
        $this->fileSystem = $imgFileSystem;
        $this->fileSystemPath = $imgFileSystemPath;
    }
    /**
     * @return ArticleTheme[]
     */
    protected function getAllowedThemes(): array
    {
        return [
            new BaseArticleTheme($this->fileSystem, $this->fileSystemPath),
            new FoodArticleTheme($this->fileSystem, $this->fileSystemPath),
            new PhpArticleTheme($this->fileSystem, $this->fileSystemPath),
        ];
    }
}