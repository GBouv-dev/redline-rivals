<?php

namespace App\Repository;

use App\Entity\BoosterType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoosterType>
 */
class BoosterTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoosterType::class);
    }

    /**
     * Récupère tous les boosters actifs
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('b.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un booster par son ID s'il est actif
     */
    public function findOneActiveById(int $id): ?BoosterType
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->andWhere('b.isActive = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère les boosters par rareté
     */
    public function findByRarity(string $rarity): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.rarity = :rarity')
            ->andWhere('b.isActive = :active')
            ->setParameter('rarity', $rarity)
            ->setParameter('active', true)
            ->orderBy('b.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
