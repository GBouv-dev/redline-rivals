<?php

namespace App\Repository;

use App\Entity\Auction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Auction>
 */
class AuctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auction::class);
    }

    /** @return Auction[] */
    public function findActiveAuctions(?string $rarity = null, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.card', 'c')
            ->where('a.status = :status')
            ->andWhere('a.endsAt > :now')
            ->setParameter('status', Auction::STATUS_ACTIVE)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('a.endsAt', 'ASC');

        if ($rarity) {
            $qb->andWhere('c.rarity = :rarity')->setParameter('rarity', $rarity);
        }
        if ($search) {
            $qb->andWhere('c.name LIKE :search OR c.brand LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /** @return Auction[] */
    public function findExpiredActive(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->andWhere('a.endsAt <= :now')
            ->setParameter('status', Auction::STATUS_ACTIVE)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    /** @return Auction[] */
    public function findBySellerOrBidder(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.bids', 'b')
            ->where('a.seller = :user')
            ->orWhere('b.bidder = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}