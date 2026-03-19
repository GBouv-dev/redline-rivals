<?php

namespace App\Entity;

use App\Repository\UserBoosterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBoosterRepository::class)]
class UserBooster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userBoosters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userBoosters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BoosterType $boosterType = null;

    #[ORM\Column]
    private ?int $quantity = 1;

    #[ORM\Column]
    private ?\DateTimeImmutable $acquiredAt = null;

    #[ORM\Column]
    private ?bool $isOpened = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $openedAt = null;

    public function __construct()
    {
        $this->acquiredAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getBoosterType(): ?BoosterType
    {
        return $this->boosterType;
    }

    public function setBoosterType(?BoosterType $boosterType): static
    {
        $this->boosterType = $boosterType;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getAcquiredAt(): ?\DateTimeImmutable
    {
        return $this->acquiredAt;
    }

    public function setAcquiredAt(\DateTimeImmutable $acquiredAt): static
    {
        $this->acquiredAt = $acquiredAt;
        return $this;
    }

    public function isOpened(): ?bool
    {
        return $this->isOpened;
    }

    public function setIsOpened(bool $isOpened): static
    {
        $this->isOpened = $isOpened;
        return $this;
    }

    public function getOpenedAt(): ?\DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function setOpenedAt(?\DateTimeImmutable $openedAt): static
    {
        $this->openedAt = $openedAt;
        return $this;
    }
}
