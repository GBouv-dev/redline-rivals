<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserBooster;
use App\Entity\BoosterType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserBooster>
 */
class UserBoosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBooster::class);
    }

    /**
     * Récupère tous les boosters non ouverts d'un utilisateur
     */
    public function findUnopenedByUser(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->leftJoin('ub.boosterType', 'bt')
            ->addSelect('bt')
            ->where('ub.user = :user')
            ->andWhere('ub.isOpened = :opened')
            ->andWhere('ub.quantity > 0')
            ->setParameter('user', $user)
            ->setParameter('opened', false)
            ->orderBy('ub.acquiredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les boosters d'un utilisateur (ouverts et non ouverts)
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->leftJoin('ub.boosterType', 'bt')
            ->addSelect('bt')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ub.isOpened', 'ASC')
            ->addOrderBy('ub.acquiredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un utilisateur possède un booster spécifique non ouvert
     */
    public function findOneUnopenedByUserAndType(User $user, BoosterType $boosterType): ?UserBooster
    {
        return $this->createQueryBuilder('ub')
            ->where('ub.user = :user')
            ->andWhere('ub.boosterType = :boosterType')
            ->andWhere('ub.isOpened = :opened')
            ->andWhere('ub.quantity > 0')
            ->setParameter('user', $user)
            ->setParameter('boosterType', $boosterType)
            ->setParameter('opened', false)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compte le nombre total de boosters non ouverts d'un utilisateur
     */
    public function countUnopenedByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('ub')
            ->select('SUM(ub.quantity)')
            ->where('ub.user = :user')
            ->andWhere('ub.isOpened = :opened')
            ->setParameter('user', $user)
            ->setParameter('opened', false)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
}
