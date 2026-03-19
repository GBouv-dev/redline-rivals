<?php

namespace App\Entity;

use App\Repository\MarketListingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketListingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MarketListing
{
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SOLD      = 'sold';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $seller = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Card $card = null;

    #[ORM\Column]
    private int $price = 0;

    /** Nombre d'exemplaires mis en vente */
    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $soldAt = null;

    #[ORM\ManyToOne]
    private ?User $buyer = null;

    // -----------------------------------------------------------------------

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters / Setters -------------------------------------------------------

    public function getId(): ?int { return $this->id; }

    public function getSeller(): ?User { return $this->seller; }
    public function setSeller(?User $seller): static { $this->seller = $seller; return $this; }

    public function getCard(): ?Card { return $this->card; }
    public function setCard(?Card $card): static { $this->card = $card; return $this; }

    public function getPrice(): int { return $this->price; }
    public function setPrice(int $price): static { $this->price = $price; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getSoldAt(): ?\DateTimeImmutable { return $this->soldAt; }
    public function setSoldAt(?\DateTimeImmutable $soldAt): static { $this->soldAt = $soldAt; return $this; }

    public function getBuyer(): ?User { return $this->buyer; }
    public function setBuyer(?User $buyer): static { $this->buyer = $buyer; return $this; }

    // Helpers -----------------------------------------------------------------

    public function isActive(): bool    { return $this->status === self::STATUS_ACTIVE; }
    public function isSold(): bool      { return $this->status === self::STATUS_SOLD; }
    public function isCancelled(): bool { return $this->status === self::STATUS_CANCELLED; }
}