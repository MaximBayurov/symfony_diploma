<?php

namespace App\Services;

use App\Entity\Article;
use League\Flysystem\Filesystem;
use Maxim\ArticleThemesBundle\ArticleTheme;
use Maxim\ArticleThemesBundle\Exceptions\ThemeNotProvidedException;
use Maxim\ArticleThemesBundle\BaseArticleThemesProvider;
use Twig\Environment;

class ArticleGenerator
{
    private array $paragraphs;
    
    public function __construct(
        private Environment $twig,
        private BaseArticleThemesProvider $articleThemesProvider,
        private PasteWords $wordsPaster,
        private Filesystem $imgFileSystem,
        private string $imgFileSystemPath,
        private ModulesProvider $modulesProvider
    ) {
    }
    
    /**
     * @param Article $article
     * @return string
     * @throws ThemeNotProvidedException
     */
    public function generate(Article $article): string
    {
        $theme = $this->articleThemesProvider->getThemeByCode(
            $article->getTheme()
        );
        
        $result = $this->renderArticleTitle($article, $theme);
        
        $userModules = $this->getUserModules($article);
        $modules = $this->modulesProvider->getModules(
            $article->getSizeFrom(),
            $article->getSizeTo(),
            $userModules
        );
        
        $this->collectModulesData($modules, $article, $theme);
        if (!$article->isEmptyWords()) {
            $this->addWords($article);
        }
        $result .= $this->renderArticle($modules);
        
        return $result;
    }
    
    public function generateDescription(Article $article): string
    {
        $keyword = $article->getKeyword();
        
        return 'Статья о ' . $keyword['prepositional'] ?? $keyword['nominative'];
    }
    
    private function renderArticleTitle(Article $article, ArticleTheme $theme): string
    {
        $articleTitle = $article->getTitle();
        if (!$articleTitle) {
            $articleTitle = $theme->getArticleTitle();
        }
        
        $template = $this->twig->createTemplate('<h1>' . $articleTitle . '</h1>');
        
        return $template->render([
            'keyword' => $article->getKeyword(),
        ]);
    }
    
    private function collectModulesData(array &$modules, Article $article, ArticleTheme $theme): void
    {
        foreach ($modules as $moduleIndex => &$module) {
            
            $paragraphsCount = random_int(2, 4);
            $paragraphs = $theme->getParagraphs($paragraphsCount);
            foreach ($paragraphs as $paragraphIndex => $item) {
                $key = 'module' . $moduleIndex . 'paragraph' . $paragraphIndex;
                $this->paragraphs[$key] = $item;
                if ($paragraphIndex == $paragraphsCount - 1) {
                    $paragraph = &$this->paragraphs[$key];
                } else {
                    $resultParagraphs[] = &$this->paragraphs[$key];
                }
            }
            
            if ($articleImages = $article->getImages()) {
                $imagesCount = count($article->getImages());
                $imageSrc = $articleImages[random_int(0, $imagesCount - 1)];
            } else {
                $imageSrc = $theme->getImage($this->imgFileSystem, $this->imgFileSystemPath);
            }
            
            $module = [
                'template' => $module,
                'context' => [
                    'title' => $theme->getTitle(),
                    'keyword' => $article->getKeyword(),
                    'paragraph' => $paragraph,
                    'paragraphs' => $resultParagraphs,
                    'imageSrc' => $imageSrc,
                ],
            ];
        }
    }
    
    private function addWords(Article $article)
    {
        foreach ($article->getWords() as $word) {
            
            if (!$word['count'] || $word['count'] < 0) {
                $word['count'] = 1;
            }
            
            $word['count'] = (int)$word['count'];
            
            for ($i = 0; $i < $word['count']; $i++) {
                $randomKey = array_rand($this->paragraphs);
                $this->paragraphs[$randomKey] =
                    $this->wordsPaster->paste($this->paragraphs[$randomKey], $word['promotedWord']);
            }
        }
    }
    
    private function renderArticle(array $modules): string
    {
        $result = '';
        foreach ($modules as $module) {
            $template = $this->twig->createTemplate($module['template']);
            
            array_walk($module['context']['paragraphs'], function (&$paragraph) {
                $paragraph = preg_match_all('/<p>.*<\/p>/', $paragraph)
                    ? $paragraph
                    : '<p>' . $paragraph . '</p>';
                unset($paragraph);
            });
            
            $module['context']['paragraphs'] = join(' ', $module['context']['paragraphs']);
            
            $result .= $template->render($module['context']);
        }
        
        return html_entity_decode($result);
    }
    
    private function getUserModules(Article $article): array
    {
        $userModules = [];
        $subscription = $article->getAuthor()->getSubscription();
        if ($subscription->isPro()
            && $subscription->isValid()) {
            
            $userModules = $article->getAuthor()->getGeneratorModules()->map(function ($item) {
                return $item->getContent();
            })->toArray();
        }
        
        return $userModules;
    }
}