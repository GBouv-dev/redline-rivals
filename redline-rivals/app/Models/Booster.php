<?php

class Booster extends Model
{
    protected static string $table = 'boosters';

    // Récupère les boosters actifs avec le nb de cartes dans leur pool
    public static function allActive(): array
    {
        return Database::query("
            SELECT b.*, (SELECT COUNT(*) FROM cards) as pool_size
            FROM boosters b
            WHERE b.is_active = 1
            ORDER BY b.price ASC
        ");
    }

    // Récupère le pool de cartes d'un booster avec leurs taux de drop
    public static function getPool(int $boosterId): array
    {
        return Database::query("
            SELECT c.*, bc.drop_rate
            FROM booster_cards bc
            JOIN cards c ON bc.card_id = c.id
            WHERE bc.booster_id = ?
            ORDER BY bc.drop_rate DESC
        ", [$boosterId]);
    }

    // Ouvre un booster : tire des cartes dans TOUT le catalogue, pondéré par rareté selon le palier
    public static function open(int $boosterId): array
    {
        $booster = static::find($boosterId);
        if (!$booster) return [];

        $pool = static::buildWeightedPool((int) $booster['price']);
        if (empty($pool)) return [];

        $drawnCards = [];
        for ($i = 0; $i < $booster['card_count']; $i++) {
            $drawnCards[] = static::drawCard($pool);
        }

        return $drawnCards;
    }

    // Pool pondéré à partir de toutes les cartes (poids = rareté × palier de prix)
    private static function buildWeightedPool(int $price): array
    {
        $weights = $price >= 350
            ? ['common' => 15, 'rare' => 30, 'epic' => 35, 'legendary' => 20]
            : ($price >= 150
                ? ['common' => 40, 'rare' => 33, 'epic' => 20, 'legendary' => 7]
                : ['common' => 60, 'rare' => 28, 'epic' => 10, 'legendary' => 2]);

        $cards = Database::query("SELECT * FROM cards");
        if (empty($cards)) return [];

        $counts = [];
        foreach ($cards as $c) {
            $counts[$c['rarity']] = ($counts[$c['rarity']] ?? 0) + 1;
        }
        // Chaque rareté pèse globalement $weights[rareté] ; réparti également entre ses cartes
        foreach ($cards as &$c) {
            $w = $weights[$c['rarity']] ?? 1;
            $c['drop_rate'] = $w / max(1, $counts[$c['rarity']]);
        }
        unset($c);

        return $cards;
    }

    // Tire une carte selon les taux de drop pondérés
    private static function drawCard(array $pool): array
    {
        $totalRate = array_sum(array_column($pool, 'drop_rate'));
        $rand = (mt_rand() / mt_getrandmax()) * $totalRate;

        $cumulative = 0;
        foreach ($pool as $card) {
            $cumulative += $card['drop_rate'];
            if ($rand <= $cumulative) {
                return $card;
            }
        }

        // Fallback sur la dernière carte
        return end($pool);
    }
}
