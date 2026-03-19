<?php

namespace App\Entity;

use App\Repository\BattleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleRepository::class)]
class Battle
{
    public const STATUS_WAITING  = 'waiting';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_FINISHED = 'finished';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $player1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $player2 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Deck $deck1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Deck $deck2 = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_WAITING;

    #[ORM\Column]
    private int $roundsP1 = 0;

    #[ORM\Column]
    private int $roundsP2 = 0;

    // JSON : { p1Hand: [], p2Hand: [], p1Played: null|id, p2Played: null|id, rounds: [] }
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $gameState = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $winner = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getPlayer1(): ?User { return $this->player1; }
    public function setPlayer1(?User $player1): static { $this->player1 = $player1; return $this; }

    public function getPlayer2(): ?User { return $this->player2; }
    public function setPlayer2(?User $player2): static { $this->player2 = $player2; return $this; }

    public function getDeck1(): ?Deck { return $this->deck1; }
    public function setDeck1(?Deck $deck1): static { $this->deck1 = $deck1; return $this; }

    public function getDeck2(): ?Deck { return $this->deck2; }
    public function setDeck2(?Deck $deck2): static { $this->deck2 = $deck2; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getRoundsP1(): int { return $this->roundsP1; }
    public function setRoundsP1(int $roundsP1): static { $this->roundsP1 = $roundsP1; return $this; }

    public function getRoundsP2(): int { return $this->roundsP2; }
    public function setRoundsP2(int $roundsP2): static { $this->roundsP2 = $roundsP2; return $this; }

    public function getGameState(): ?array { return $this->gameState; }
    public function setGameState(?array $gameState): static { $this->gameState = $gameState; return $this; }

    public function getWinner(): ?User { return $this->winner; }
    public function setWinner(?User $winner): static { $this->winner = $winner; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
}