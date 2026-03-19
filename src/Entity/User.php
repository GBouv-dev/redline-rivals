<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'integer', options: ['default' => 1000])]
    private int $credits = 1000;

    /**
     * @var Collection<int, Deck>
     */
    #[ORM\OneToMany(targetEntity: Deck::class, mappedBy: 'owner')]
    private Collection $decks;

    /**
     * @var Collection<int, UserBooster>
     */
    #[ORM\OneToMany(targetEntity: UserBooster::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $userBoosters;

    /**
     * @var Collection<int, UserCard>
     */
    #[ORM\OneToMany(targetEntity: UserCard::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $userCards;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $avatar = null;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
        $this->userBoosters = new ArrayCollection();
        $this->userCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @return Collection<int, Deck>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): static
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->setOwner($this);
        }

        return $this;
    }

    public function removeDeck(Deck $deck): static
    {
        if ($this->decks->removeElement($deck)) {
            // set the owning side to null (unless already changed)
            if ($deck->getOwner() === $this) {
                $deck->setOwner(null);
            }
        }

        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function addCredits(int $amount): static
    {
        $this->credits += $amount;

        return $this;
    }

    public function removeCredits(int $amount): static
    {
        $this->credits -= $amount;

        return $this;
    }

    public function hasEnoughCredits(int $amount): bool
    {
        return $this->credits >= $amount;
    }

        // Getters et setters pour userBoosters
    /**
     * @return Collection<int, UserBooster>
     */
    public function getUserBoosters(): Collection
    {
        return $this->userBoosters;
    }

    public function addUserBooster(UserBooster $userBooster): static
    {
        if (!$this->userBoosters->contains($userBooster)) {
            $this->userBoosters->add($userBooster);
            $userBooster->setUser($this);
        }
        return $this;
    }

    public function removeUserBooster(UserBooster $userBooster): static
    {
        if ($this->userBoosters->removeElement($userBooster)) {
            if ($userBooster->getUser() === $this) {
                $userBooster->setUser(null);
            }
        }
        return $this;
    }

        // Getters et setters pour userCards
    /**
     * @return Collection<int, UserCard>
     */
    public function getUserCards(): Collection
    {
        return $this->userCards;
    }

    public function addUserCard(UserCard $userCard): static
    {
        if (!$this->userCards->contains($userCard)) {
            $this->userCards->add($userCard);
            $userCard->setUser($this);
        }
        return $this;
    }

    public function removeUserCard(UserCard $userCard): static
    {
        if ($this->userCards->removeElement($userCard)) {
            if ($userCard->getUser() === $this) {
                $userCard->setUser(null);
            }
        }
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}