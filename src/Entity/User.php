<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Аккаунт с такой почтой уже существует")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Адрес электронной почты не может быть пустым")
     * @Assert\Email(message="Некорректный адрес электронной почты")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Ваше имя не может быть пустым")
     */
    private $fullName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToOne(targetEntity=ApiToken::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $apiToken;

    /**
     * @ORM\OneToOne(targetEntity=Subscription::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Subscription $subscription;

    /**
     * @ORM\OneToMany(targetEntity=GeneratorModule::class, mappedBy="user")
     */
    private $generatorModules;

    public function __construct()
    {
        $this->generatorModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getApiToken(): ?ApiToken
    {
        return $this->apiToken;
    }

    public function setApiToken(ApiToken $apiToken): self
    {
        // set the owning side of the relation if necessary
        if ($apiToken->getUser() !== $this) {
            $apiToken->setUser($this);
        }

        $this->apiToken = $apiToken;

        return $this;
    }

    public function getSubscription(): Subscription
    {
        return
            $this->subscription && $this->subscription->isValid()
            ? $this->subscription
            : (new Subscription())
                ->setLevel(Subscription::LEVEL_TYPES['FREE']['TEXT'])
                ->setUser($this)
        ;
    }

    public function setSubscription(Subscription $subscription): self
    {
        // set the owning side of the relation if necessary
        if ($subscription->getUser() !== $this) {
            $subscription->setUser($this);
        }

        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return Collection|GeneratorModule[]
     */
    public function getGeneratorModules(): Collection
    {
        return $this->generatorModules;
    }

    public function addGeneratorModule(GeneratorModule $generatorModule): self
    {
        if (!$this->generatorModules->contains($generatorModule)) {
            $this->generatorModules[] = $generatorModule;
            $generatorModule->setUser($this);
        }

        return $this;
    }

    public function removeGeneratorModule(GeneratorModule $generatorModule): self
    {
        if ($this->generatorModules->removeElement($generatorModule)) {
            // set the owning side to null (unless already changed)
            if ($generatorModule->getUser() === $this) {
                $generatorModule->setUser(null);
            }
        }

        return $this;
    }
}
