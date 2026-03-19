<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\MarketListing;
use App\Entity\User;
use App\Entity\UserCard;
use App\Repository\MarketListingRepository;
use App\Repository\UserCardRepository;
use Doctrine\ORM\EntityManagerInterface;

class MarketService
{
    public function __construct(
        private EntityManagerInterface    $em,
        private MarketListingRepository   $marketListingRepository,
        private UserCardRepository        $userCardRepository,
    ) {}

    // -------------------------------------------------------------------------
    // CRÉATION D'ANNONCE
    // -------------------------------------------------------------------------

    /**
     * @throws \RuntimeException
     */
    public function createListing(User $seller, Card $card, int $price, int $quantity = 1): MarketListing
    {
        if ($price <= 0) {
            throw new \RuntimeException('Le prix doit être supérieur à 0 crédits.');
        }
        if ($quantity <= 0) {
            throw new \RuntimeException('La quantité doit être supérieure à 0.');
        }

        // Vérifier que le vendeur possède assez d'exemplaires
        $userCard = $this->userCardRepository->findOneBy(['user' => $seller, 'card' => $card]);
        if (!$userCard || $userCard->getQuantity() < $quantity) {
            throw new \RuntimeException('Vous ne possédez pas assez d\'exemplaires de cette carte.');
        }

        // Déduire la quantité de l'inventaire du vendeur
        $userCard->setQuantity($userCard->getQuantity() - $quantity);
        if ($userCard->getQuantity() === 0) {
            $this->em->remove($userCard);
        }

        // Créer l'annonce
        $listing = new MarketListing();
        $listing->setSeller($seller)
                ->setCard($card)
                ->setPrice($price)
                ->setQuantity($quantity)
                ->setStatus(MarketListing::STATUS_ACTIVE);

        $this->em->persist($listing);
        $this->em->flush();

        return $listing;
    }

    // -------------------------------------------------------------------------
    // ACHAT
    // -------------------------------------------------------------------------

    /**
     * @throws \RuntimeException
     */
    public function purchaseListing(User $buyer, MarketListing $listing): void
    {
        if (!$listing->isActive()) {
            throw new \RuntimeException('Cette annonce n\'est plus disponible.');
        }
        if ($listing->getSeller() === $buyer) {
            throw new \RuntimeException('Vous ne pouvez pas acheter votre propre annonce.');
        }
        if ($buyer->getCredits() < $listing->getPrice()) {
            throw new \RuntimeException(sprintf(
                'Crédits insuffisants. Vous avez %d ¢, il en faut %d ¢.',
                $buyer->getCredits(),
                $listing->getPrice()
            ));
        }

        // Débiter l'acheteur
        $buyer->setCredits($buyer->getCredits() - $listing->getPrice());

        // Créditer le vendeur
        $seller = $listing->getSeller();
        $seller->setCredits($seller->getCredits() + $listing->getPrice());

        // Ajouter la carte à l'inventaire de l'acheteur
        $this->addCardToUser($buyer, $listing->getCard(), $listing->getQuantity());

        // Marquer l'annonce comme vendue
        $listing->setStatus(MarketListing::STATUS_SOLD)
                ->setBuyer($buyer)
                ->setSoldAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    // -------------------------------------------------------------------------
    // ANNULATION
    // -------------------------------------------------------------------------

    /**
     * @throws \RuntimeException
     */
    public function cancelListing(User $user, MarketListing $listing): void
    {
        if (!$listing->isActive()) {
            throw new \RuntimeException('Cette annonce ne peut plus être annulée.');
        }
        if ($listing->getSeller() !== $user) {
            throw new \RuntimeException('Vous n\'êtes pas le propriétaire de cette annonce.');
        }

        // Restituer la carte au vendeur
        $this->addCardToUser($user, $listing->getCard(), $listing->getQuantity());

        $listing->setStatus(MarketListing::STATUS_CANCELLED);
        $this->em->flush();
    }

    // -------------------------------------------------------------------------
    // LECTURE
    // -------------------------------------------------------------------------

    /** @return MarketListing[] */
    public function getActiveListings(
        ?string $rarity   = null,
        ?string $brand    = null,
        ?int    $maxPrice = null,
        ?string $search   = null,
    ): array {
        return $this->marketListingRepository->findActiveListings($rarity, $brand, $maxPrice, $search);
    }

    /** @return MarketListing[] */
    public function getUserListings(User $user): array
    {
        return $this->marketListingRepository->findBySellerOrderedByDate($user);
    }

    /** Cartes que l'utilisateur peut mettre en vente (qty > 0). */
    public function getSellableCards(User $user): array
    {
        return $this->userCardRepository->findBy(['user' => $user]);
    }

    public function getCardById(int $id): ?Card
    {
        return $this->em->getRepository(Card::class)->find($id);
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