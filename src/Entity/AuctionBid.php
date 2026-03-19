<?php

namespace App\Entity;

use App\Repository\AuctionBidRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuctionBidRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AuctionBid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bids')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Auction $auction = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $bidder = null;

    #[ORM\Column]
    private int $amount = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $placedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->placedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getAuction(): ?Auction { return $this->auction; }
    public function setAuction(?Auction $auction): static { $this->auction = $auction; return $this; }

    public function getBidder(): ?User { return $this->bidder; }
    public function setBidder(?User $bidder): static { $this->bidder = $bidder; return $this; }

    public function getAmount(): int { return $this->amount; }
    public function setAmount(int $amount): static { $this->amount = $amount; return $this; }

    public function getPlacedAt(): ?\DateTimeImmutable { return $this->placedAt; }
}