<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class DemoArticle
{
    
    /**
     * @Assert\NotBlank(message="Укажите заголовок статьи")
     */
    private string $title;
    
    /**
     * @Assert\NotBlank(message="Укажите продвигаемое слово")
     */
    private string $words;
    
    private array $content;
    
    /**
     * @return string
     */
    public function getWords(): string
    {
        return $this->words;
    }
    
    /**
     * @param string $words
     */
    public function setWords(string $words): void
    {
        $this->words = $words;
    }
    
    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    
    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }
    
    /**
     * @param array $content
     */
    public function setContent(array $content): void
    {
        
        $this->content = $content;
    }
    
    public function __sleep(): array
    {
        return [
            'title',
            'words',
        ];
    }
}