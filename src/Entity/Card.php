<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?int $horsepower = null;

    #[ORM\Column(length: 15)]
    private ?string $rarity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    /** Vitesse max en km/h */
    #[ORM\Column(nullable: true)]
    private ?int $speed = null;

    /** 0-100 km/h en secondes (ex: 3.5) — plus bas = mieux */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $acceleration = null;

    /** Maniabilité score 1-100 */
    #[ORM\Column(nullable: true)]
    private ?int $handling = null;

    /** Poids en kg */
    #[ORM\Column(nullable: true)]
    private ?int $weight = null;

    /** @var Collection<int, Deck> */
    #[ORM\ManyToMany(targetEntity: Deck::class, mappedBy: 'cards')]
    private Collection $decks;

    /** @var Collection<int, UserCard> */
    #[ORM\OneToMany(targetEntity: UserCard::class, mappedBy: 'card')]
    private Collection $userCards;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
        $this->userCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }
    public function setBrand(string $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function getHorsepower(): ?int
    {
        return $this->horsepower;
    }
    public function setHorsepower(int $horsepower): static
    {
        $this->horsepower = $horsepower;
        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }
    public function setRarity(string $rarity): static
    {
        $this->rarity = $rarity;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }
    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }
    public function setSpeed(?int $speed): static
    {
        $this->speed = $speed;
        return $this;
    }

    public function getAcceleration(): ?float
    {
        return $this->acceleration;
    }
    public function setAcceleration(?float $acceleration): static
    {
        $this->acceleration = $acceleration;
        return $this;
    }

    public function getHandling(): ?int
    {
        return $this->handling;
    }
    public function setHandling(?int $handling): static
    {
        $this->handling = $handling;
        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }
    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;
        return $this;
    }

    /** @return Collection<int, Deck> */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(Deck $deck): static
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
            $deck->addCard($this);
        }
        return $this;
    }

    public function removeDeck(Deck $deck): static
    {
        if ($this->decks->removeElement($deck))
            $deck->removeCard($this);
        return $this;
    }

    /** @return Collection<int, UserCard> */
    public function getUserCards(): Collection
    {
        return $this->userCards;
    }

    public function addUserCard(UserCard $userCard): static
    {
        if (!$this->userCards->contains($userCard)) {
            $this->userCards->add($userCard);
            $userCard->setCard($this);
        }
        return $this;
    }

    public function removeUserCard(UserCard $userCard): static
    {
        if ($this->userCards->removeElement($userCard)) {
            if ($userCard->getCard() === $this)
                $userCard->setCard(null);
        }
        return $this;
    }
}