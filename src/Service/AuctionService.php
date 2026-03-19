<?php

namespace App\Service;

use App\Entity\Auction;
use App\Entity\AuctionBid;
use App\Entity\Card;
use App\Entity\User;
use App\Entity\UserCard;
use App\Repository\AuctionRepository;
use App\Repository\UserCardRepository;
use Doctrine\ORM\EntityManagerInterface;

class AuctionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AuctionRepository      $auctionRepository,
        private UserCardRepository     $userCardRepository,
    ) {}

    // -------------------------------------------------------------------------
    // CREATION
    // -------------------------------------------------------------------------

    /**
     * @throws \RuntimeException
     */
    public function createAuction(
        User $seller,
        Card $card,
        int  $startPrice,
        int  $durationHours = 24,
        int  $quantity = 1,
    ): Auction {
        if ($startPrice <= 0) {
            throw new \RuntimeException('Le prix de depart doit etre superieur a 0.');
        }
        if ($durationHours < 1 || $durationHours > 168) {
            throw new \RuntimeException('La duree doit etre entre 1h et 7 jours.');
        }

        $userCard = $this->userCardRepository->findOneBy(['user' => $seller, 'card' => $card]);
        if (!$userCard || $userCard->getQuantity() < $quantity) {
            throw new \RuntimeException('Vous ne possedez pas assez d\'exemplaires de cette carte.');
        }

        // Retirer la carte de l'inventaire du vendeur
        $userCard->setQuantity($userCard->getQuantity() - $quantity);
        if ($userCard->getQuantity() === 0) {
            $this->em->remove($userCard);
        }

        $auction = new Auction();
        $auction->setSeller($seller)
                ->setCard($card)
                ->setStartPrice($startPrice)
                ->setCurrentPrice($startPrice)
                ->setQuantity($quantity)
                ->setEndsAt(new \DateTimeImmutable("+{$durationHours} hours"))
                ->setStatus(Auction::STATUS_ACTIVE);

        $this->em->persist($auction);
        $this->em->flush();

        return $auction;
    }

    // -------------------------------------------------------------------------
    // ENCHERE
    // -------------------------------------------------------------------------

    /**
     * @throws \RuntimeException
     */
    public function placeBid(User $bidder, Auction $auction, int $amount): AuctionBid
    {
        if (!$auction->isActive()) {
            throw new \RuntimeException('Cette enchère est terminée ou expirée.');
        }
        if ($auction->getSeller() === $bidder) {
            throw new \RuntimeException('Vous ne pouvez pas enchérir sur votre propre enchère.');
        }
        if ($amount < $auction->getMinimumNextBid()) {
            throw new \RuntimeException(sprintf(
                'L\'enchère minimum est de %d ¢.',
                $auction->getMinimumNextBid()
            ));
        }
        if ($bidder->getCredits() < $amount) {
            throw new \RuntimeException(sprintf(
                'Crédits insuffisants. Vous avez %d ¢, il en faut %d ¢.',
                $bidder->getCredits(),
                $amount
            ));
        }

        // Rembourser le précédent enchérisseur
        $previousBidder = $auction->getHighestBidder();
        if ($previousBidder && $previousBidder !== $bidder) {
            $previousBidder->setCredits($previousBidder->getCredits() + $auction->getCurrentPrice());
        }

        // Débiter le nouvel enchérisseur
        $bidder->setCredits($bidder->getCredits() - $amount);

        // Enregistrer l'enchère
        $bid = new AuctionBid();
        $bid->setAuction($auction)
            ->setBidder($bidder)
            ->setAmount($amount);

        $auction->setCurrentPrice($amount);

        $this->em->persist($bid);
        $this->em->flush();

        return $bid;
    }

    // -------------------------------------------------------------------------
    // CLOTURE
    // -------------------------------------------------------------------------

    public function closeAuction(Auction $auction): void
    {
        if (!$auction->isExpired()) {
            return;
        }

        $winner = $auction->getHighestBidder();

        if ($winner) {
            // Donner la carte au gagnant
            $this->addCardToUser($winner, $auction->getCard(), $auction->getQuantity());
            // Créditer le vendeur
            $auction->getSeller()->setCredits(
                $auction->getSeller()->getCredits() + $auction->getCurrentPrice()
            );
            $auction->setWinner($winner);
        } else {
            // Aucune enchère — rendre la carte au vendeur
            $this->addCardToUser($auction->getSeller(), $auction->getCard(), $auction->getQuantity());
        }

        $auction->setStatus(Auction::STATUS_ENDED);
        $this->em->flush();
    }

    /** Clôture toutes les enchères expirées (appelable via une commande). */
    public function closeExpiredAuctions(): int
    {
        $expired = $this->auctionRepository->findExpiredActive();
        foreach ($expired as $auction) {
            $this->closeAuction($auction);
        }
        return count($expired);
    }

    // -------------------------------------------------------------------------
    // LECTURE
    // -------------------------------------------------------------------------

    /** @return Auction[] */
    public function getActiveAuctions(?string $rarity = null, ?string $search = null): array
    {
        // Clore les expirées à la volée avant d'afficher
        $this->closeExpiredAuctions();
        return $this->auctionRepository->findActiveAuctions($rarity, $search);
    }

    /** @return Auction[] */
    public function getUserAuctions(User $user): array
    {
        return $this->auctionRepository->findBySellerOrBidder($user);
    }

    public function getCardById(int $id): ?Card
    {
        return $this->em->getRepository(Card::class)->find($id);
    }

    public function getSellableCards(User $user): array
    {
        return $this->userCardRepository->findBy(['user' => $user]);
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function addCardToUser(User $user, Card $card, int $quantity): void
    {
        $userCard = $this->userCardRepository->findOneBy(['user' => $user, 'card' => $card]);
        if ($userCard) {
            $userCard->setQuantity($userCard->getQuantity() + $quantity);
        } else {
            $userCard = new UserCard();
            $userCard->setUser($user)->setCard($card)->setQuantity($quantity);
            $this->em->persist($userCard);
        }
    }
}