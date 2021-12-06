<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    public const LEVEL_TYPES = [
        'FREE' => [
            'TEXT' => 'FREE',
            'VALUE' => 0,
            'PRICE' => 0,
            'SORT' => 10,
        ],
        'PLUS' => [
            'TEXT' => 'PLUS',
            'VALUE' => 1,
            'PRICE' => 9,
            'SORT' => 20,
        ],
        'PRO' => [
            'TEXT' => 'PRO',
            'VALUE' => 2,
            'PRICE' => 49,
            'SORT' => 30,
        ],
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="subscription", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;
    
    public static function isValidType(mixed $type):bool
    {
        return is_string($type) && isset(self::LEVEL_TYPES[$type]);
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getLevel(): ?array
    {
        return in_array($this->level, array_keys(self::LEVEL_TYPES))
            ? self::LEVEL_TYPES[$this->level]
            : null
        ;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }
    
    public function isFree(): bool
    {
        return $this->level === self::LEVEL_TYPES['FREE']['TEXT'];
    }
    
    public function isValid(): bool
    {
        return $this->getExpiresAt() > new \DateTimeImmutable('now');
    }
}
