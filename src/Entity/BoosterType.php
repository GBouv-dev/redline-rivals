<?php

namespace App\Entity;

use App\Repository\BoosterTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoosterTypeRepository::class)]
class BoosterType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $cardCount = null;

    #[ORM\Column(length: 50)]
    private ?string $rarity = null; // 'standard', 'premium', 'legendary'

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $color = null; // Couleur thème cyberpunk

    #[ORM\Column]
    private ?bool $isActive = true;

    /**
     * @var Collection<int, UserBooster>
     */
    #[ORM\OneToMany(targetEntity: UserBooster::class, mappedBy: 'boosterType')]
    private Collection $userBoosters;

    // Probabilités de rarité des cartes (en pourcentage)
    #[ORM\Column]
    private ?int $commonChance = 70;

    #[ORM\Column]
    private ?int $rareChance = 20;

    #[ORM\Column]
    private ?int $epicChance = 8;

    #[ORM\Column]
    private ?int $legendaryChance = 2;

    public function __construct()
    {
        $this->userBoosters = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getCardCount(): ?int
    {
        return $this->cardCount;
    }

    public function setCardCount(int $cardCount): static
    {
        $this->cardCount = $cardCount;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

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
            $userBooster->setBoosterType($this);
        }

        return $this;
    }

    public function removeUserBooster(UserBooster $userBooster): static
    {
        if ($this->userBoosters->removeElement($userBooster)) {
            if ($userBooster->getBoosterType() === $this) {
                $userBooster->setBoosterType(null);
            }
        }

        return $this;
    }

    public function getCommonChance(): ?int
    {
        return $this->commonChance;
    }

    public function setCommonChance(int $commonChance): static
    {
        $this->commonChance = $commonChance;
        return $this;
    }

    public function getRareChance(): ?int
    {
        return $this->rareChance;
    }

    public function setRareChance(int $rareChance): static
    {
        $this->rareChance = $rareChance;
        return $this;
    }

    public function getEpicChance(): ?int
    {
        return $this->epicChance;
    }

    public function setEpicChance(int $epicChance): static
    {
        $this->epicChance = $epicChance;
        return $this;
    }

    public function getLegendaryChance(): ?int
    {
        return $this->legendaryChance;
    }

    public function setLegendaryChance(int $legendaryChance): static
    {
        $this->legendaryChance = $legendaryChance;
        return $this;
    }
}
