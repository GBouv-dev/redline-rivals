<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserCard;
use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCard>
 */
class UserCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCard::class);
    }

    /**
     * Récupère toutes les cartes d'un utilisateur
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('uc')
            ->leftJoin('uc.card', 'c')
            ->addSelect('c')
            ->where('uc.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.rarity', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une carte spécifique d'un utilisateur
     */
    public function findOneByUserAndCard(User $user, Card $card): ?UserCard
    {
        return $this->createQueryBuilder('uc')
            ->where('uc.user = :user')
            ->andWhere('uc.card = :card')
            ->setParameter('user', $user)
            ->setParameter('card', $card)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre total de cartes d'un utilisateur
     */
    public function countByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('uc')
            ->select('SUM(uc.quantity)')
            ->where('uc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    /**
     * Récupère les cartes par rareté pour un utilisateur
     */
    public function findByUserAndRarity(User $user, string $rarity): array
    {
        return $this->createQueryBuilder('uc')
            ->leftJoin('uc.card', 'c')
            ->addSelect('c')
            ->where('uc.user = :user')
            ->andWhere('c.rarity = :rarity')
            ->setParameter('user', $user)
            ->setParameter('rarity', $rarity)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
