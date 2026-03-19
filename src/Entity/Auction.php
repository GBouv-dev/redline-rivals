<?php

namespace App\Entity;

use App\Repository\AuctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuctionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Auction
{
    public const STATUS_ACTIVE  = 'active';
    public const STATUS_ENDED   = 'ended';
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
    private int $startPrice = 0;

    #[ORM\Column]
    private int $currentPrice = 0;

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endsAt = null;

    #[ORM\ManyToOne]
    private ?User $winner = null;

    #[ORM\OneToMany(mappedBy: 'auction', targetEntity: AuctionBid::class, orphanRemoval: true)]
    #[ORM\OrderBy(['amount' => 'DESC'])]
    private Collection $bids;

    public function __construct()
    {
        $this->bids = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Helpers

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->endsAt > new \DateTimeImmutable();
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->endsAt <= new \DateTimeImmutable();
    }

    public function getSecondsRemaining(): int
    {
        $diff = $this->endsAt->getTimestamp() - time();
        return max(0, $diff);
    }

    public function getHighestBidder(): ?User
    {
        $topBid = $this->bids->first();
        return $topBid ? $topBid->getBidder() : null;
    }

    public function getBidCount(): int
    {
        return $this->bids->count();
    }

    public function getMinimumNextBid(): int
    {
        return $this->currentPrice + max(1, (int) round($this->currentPrice * 0.05));
    }

    // Getters / Setters

    public function getId(): ?int { return $this->id; }

    public function getSeller(): ?User { return $this->seller; }
    public function setSeller(?User $seller): static { $this->seller = $seller; return $this; }

    public function getCard(): ?Card { return $this->card; }
    public function setCard(?Card $card): static { $this->card = $card; return $this; }

    public function getStartPrice(): int { return $this->startPrice; }
    public function setStartPrice(int $startPrice): static { $this->startPrice = $startPrice; return $this; }

    public function getCurrentPrice(): int { return $this->currentPrice; }
    public function setCurrentPrice(int $currentPrice): static { $this->currentPrice = $currentPrice; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getEndsAt(): ?\DateTimeImmutable { return $this->endsAt; }
    public function setEndsAt(?\DateTimeImmutable $endsAt): static { $this->endsAt = $endsAt; return $this; }

    public function getWinner(): ?User { return $this->winner; }
    public function setWinner(?User $winner): static { $this->winner = $winner; return $this; }

    public function getBids(): Collection { return $this->bids; }
}