<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\BoosterType;
use App\Entity\UserBooster;
use App\Entity\Card;
use App\Entity\UserCard;
use App\Repository\BoosterTypeRepository;
use App\Repository\UserBoosterRepository;
use App\Repository\CardRepository;
use App\Repository\UserCardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BoosterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BoosterTypeRepository $boosterTypeRepository,
        private UserBoosterRepository $userBoosterRepository,
        private CardRepository $cardRepository,
        private UserCardRepository $userCardRepository
    ) {}

    /**
     * Achète un booster pour un utilisateur
     */
    public function purchaseBooster(User $user, BoosterType $boosterType): UserBooster
    {
        // Vérifier si le booster est actif
        if (!$boosterType->isActive()) {
            throw new \RuntimeException('Ce booster n\'est plus disponible');
        }

        // Vérifier si l'utilisateur a assez de crédits
        if ($user->getCredits() < $boosterType->getPrice()) {
            throw new \RuntimeException('Crédits insuffisants');
        }

        // Déduire les crédits
        $user->setCredits($user->getCredits() - $boosterType->getPrice());

        // Vérifier si l'utilisateur possède déjà ce type de booster non ouvert
        $existingBooster = $this->userBoosterRepository->findOneUnopenedByUserAndType($user, $boosterType);

        if ($existingBooster) {
            // Incrémenter la quantité
            $existingBooster->setQuantity($existingBooster->getQuantity() + 1);
            $userBooster = $existingBooster;
        } else {
            // Créer un nouveau booster
            $userBooster = new UserBooster();
            $userBooster->setUser($user);
            $userBooster->setBoosterType($boosterType);
            $userBooster->setQuantity(1);
            $this->entityManager->persist($userBooster);
        }

        $this->entityManager->flush();

        return $userBooster;
    }

    /**
     * Ouvre un booster et génère les cartes
     */
    public function openBooster(User $user, UserBooster $userBooster): array
    {
        // Vérifications de sécurité
        if ($userBooster->getUser() !== $user) {
            throw new AccessDeniedException('Ce booster ne vous appartient pas');
        }

        if ($userBooster->isOpened() || $userBooster->getQuantity() <= 0) {
            throw new \RuntimeException('Ce booster a déjà été ouvert ou n\'est plus disponible');
        }

        $boosterType = $userBooster->getBoosterType();
        $cardCount = $boosterType->getCardCount();

        // Générer les cartes
        $generatedCards = [];
        for ($i = 0; $i < $cardCount; $i++) {
            $card = $this->generateRandomCard($boosterType);
            if ($card) {
                $generatedCards[] = $this->addCardToUser($user, $card);
            }
        }

        // Décrémenter la quantité ou marquer comme ouvert
        if ($userBooster->getQuantity() > 1) {
            $userBooster->setQuantity($userBooster->getQuantity() - 1);
        } else {
            $userBooster->setIsOpened(true);
            $userBooster->setOpenedAt(new \DateTimeImmutable());
            $userBooster->setQuantity(0);
        }

        $this->entityManager->flush();

        return $generatedCards;
    }

    /**
     * Génère une carte aléatoire selon les probabilités du booster
     */
    private function generateRandomCard(BoosterType $boosterType): ?Card
    {
        // Déterminer la rareté selon les probabilités
        $rarity = $this->determineRarity($boosterType);

        // Récupérer une carte aléatoire de cette rareté
        $cards = $this->cardRepository->findByRarity($rarity);

        if (empty($cards)) {
            return null;
        }

        // Sélectionner une carte aléatoire
        return $cards[array_rand($cards)];
    }

    /**
     * Détermine la rareté d'une carte selon les probabilités
     */
    private function determineRarity(BoosterType $boosterType): string
    {
        $rand = mt_rand(1, 100);
        $cumulative = 0;

        // Vérifier legendary
        $cumulative += $boosterType->getLegendaryChance();
        if ($rand <= $cumulative) {
            return 'legendary';
        }

        // Vérifier epic
        $cumulative += $boosterType->getEpicChance();
        if ($rand <= $cumulative) {
            return 'epic';
        }

        // Vérifier rare
        $cumulative += $boosterType->getRareChance();
        if ($rand <= $cumulative) {
            return 'rare';
        }

        // Par défaut: common
        return 'common';
    }

    /**
     * Ajoute une carte à la collection d'un utilisateur
     */
    private function addCardToUser(User $user, Card $card): UserCard
    {
        // Vérifier si l'utilisateur possède déjà cette carte
        $existingUserCard = $this->userCardRepository->findOneBy([
            'user' => $user,
            'card' => $card
        ]);

        if ($existingUserCard) {
            // Incrémenter la quantité
            $existingUserCard->setQuantity($existingUserCard->getQuantity() + 1);
            $userCard = $existingUserCard;
        } else {
            // Créer une nouvelle entrée
            $userCard = new UserCard();
            $userCard->setUser($user);
            $userCard->setCard($card);
            $userCard->setQuantity(1);
            $this->entityManager->persist($userCard);
        }

        return $userCard;
    }

    /**
     * Récupère tous les boosters disponibles
     */
    public function getAvailableBoosters(): array
    {
        return $this->boosterTypeRepository->findAllActive();
    }

    /**
     * Récupère les boosters non ouverts d'un utilisateur
     */
    public function getUserBoosters(User $user): array
    {
        return $this->userBoosterRepository->findUnopenedByUser($user);
    }

    /**
     * Compte le nombre de boosters non ouverts
     */
    public function countUserBoosters(User $user): int
    {
        return $this->userBoosterRepository->countUnopenedByUser($user);
    }
}
