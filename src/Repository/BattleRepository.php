<?php

namespace App\Repository;

use App\Entity\Battle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BattleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Battle::class);
    }

    /** Toutes les battles en attente d'un 2e joueur (hors celles créées par $user). */
    public function findWaitingBattles(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')
            ->andWhere('b.player1 != :user')
            ->setParameter('status', Battle::STATUS_WAITING)
            ->setParameter('user', $user)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Battles actives ou en attente où $user participe. */
    public function findUserActiveBattles(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->where('(b.player1 = :user OR b.player2 = :user)')
            ->andWhere('b.status != :finished')
            ->setParameter('user', $user)
            ->setParameter('finished', Battle::STATUS_FINISHED)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}