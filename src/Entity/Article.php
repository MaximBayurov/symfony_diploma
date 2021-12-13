<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Validator\MoreThanSizeFrom;
use App\Validator\Words;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    use TimestampableEntity;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $theme;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $keyword = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(value="1", message="Количество модулей должно быть больше нуля")
     * @Assert\NotBlank (message="Не указано количество модулей")
     */
    private $sizeFrom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value="1", message="Количество модулей должно быть больше нуля")
     * @MoreThanSizeFrom()
     */
    private $sizeTo;

    /**
     * @ORM\Column(type="json")
     * @Words()
     */
    private $words = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Assert\Count(max=5, maxMessage="Изображений не может быть больше пяти")
     */
    private $images = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getKeyword(): ?array
    {
        return $this->keyword;
    }

    public function setKeyword(array $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSizeFrom(): ?int
    {
        return $this->sizeFrom;
    }

    public function setSizeFrom(int $sizeFrom): self
    {
        $this->sizeFrom = $sizeFrom;

        return $this;
    }

    public function getSizeTo(): ?int
    {
        if (empty($this->sizeTo)) {
            return $this->sizeFrom;
        }
        
        return $this->sizeTo;
    }

    public function setSizeTo(?int $sizeTo): self
    {
        $this->sizeTo = $sizeTo;

        return $this;
    }

    public function getWords(): ?array
    {
        $default = [
          [
              'promotedWord' => '',
              'count' => '',
          ]
        ];
        
        return ! empty($this->words)
            ? $this->words
            : $default;
    }
    
    public function isEmptyWords(): bool
    {
        $words = $this->getWords();
        $first = reset($words);
        return empty($first['promotedWord']);
    }

    public function setWords(array $words): self
    {
        $this->words = $words;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
