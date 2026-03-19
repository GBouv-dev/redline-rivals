<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    /**
     * Récupère toutes les cartes
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.rarity', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les cartes par rareté
     * Utilisé par le BoosterService pour générer des cartes aléatoires
     */
    public function findByRarity(string $rarity): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.rarity = :rarity')
            ->setParameter('rarity', $rarity)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une carte aléatoire d'une rareté donnée
     * Méthode alternative plus performante
     */
    public function findOneRandomByRarity(string $rarity): ?Card
    {
        $cards = $this->findByRarity($rarity);
        
        if (empty($cards)) {
            return null;
        }
        
        return $cards[array_rand($cards)];
    }

    /**
     * Compte le nombre de cartes par rareté
     */
    public function countByRarity(string $rarity): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.rarity = :rarity')
            ->setParameter('rarity', $rarity)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère toutes les raretés disponibles
     */
    public function findAllRarities(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT c.rarity')
            ->getQuery()
            ->getResult();

        return array_column($result, 'rarity');
    }
}
