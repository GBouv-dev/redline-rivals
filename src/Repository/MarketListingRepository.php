<?php

namespace App\Repository;

use App\Entity\MarketListing;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketListing>
 */
class MarketListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketListing::class);
    }

    /**
     * Toutes les annonces actives, triées par date décroissante.
     * Filtres optionnels : rareté, marque, prix max.
     *
     * @return MarketListing[]
     */
    public function findActiveListings(
        ?string $rarity   = null,
        ?string $brand    = null,
        ?int    $maxPrice = null,
        ?string $search   = null,
    ): array {
        $qb = $this->createQueryBuilder('ml')
            ->join('ml.card', 'c')
            ->join('ml.seller', 's')
            ->where('ml.status = :status')
            ->setParameter('status', MarketListing::STATUS_ACTIVE)
            ->orderBy('ml.createdAt', 'DESC');

        if ($rarity) {
            $qb->andWhere('c.rarity = :rarity')->setParameter('rarity', $rarity);
        }
        if ($brand) {
            $qb->andWhere('c.brand = :brand')->setParameter('brand', $brand);
        }
        if ($maxPrice) {
            $qb->andWhere('ml.price <= :maxPrice')->setParameter('maxPrice', $maxPrice);
        }
        if ($search) {
            $qb->andWhere('c.name LIKE :search OR c.brand LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Annonces d'un vendeur (toutes).
     *
     * @return MarketListing[]
     */
    public function findBySellerOrderedByDate(User $seller): array
    {
        return $this->createQueryBuilder('ml')
            ->where('ml.seller = :seller')
            ->setParameter('seller', $seller)
            ->orderBy('ml.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Annonces actives d'un vendeur.
     *
     * @return MarketListing[]
     */
    public function findActiveBySellerAndCard(User $seller, int $cardId): array
    {
        return $this->createQueryBuilder('ml')
            ->join('ml.card', 'c')
            ->where('ml.seller = :seller')
            ->andWhere('ml.status = :status')
            ->andWhere('c.id = :cardId')
            ->setParameter('seller', $seller)
            ->setParameter('status', MarketListing::STATUS_ACTIVE)
            ->setParameter('cardId', $cardId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Prix moyen d'une carte sur les 30 derniers jours.
     */
    public function getAveragePrice(int $cardId): ?float
    {
        $result = $this->createQueryBuilder('ml')
            ->select('AVG(ml.price)')
            ->join('ml.card', 'c')
            ->where('c.id = :cardId')
            ->andWhere('ml.status = :status')
            ->andWhere('ml.soldAt > :since')
            ->setParameter('cardId', $cardId)
            ->setParameter('status', MarketListing::STATUS_SOLD)
            ->setParameter('since', new \DateTimeImmutable('-30 days'))
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round((float) $result) : null;
    }
}