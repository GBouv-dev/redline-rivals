<?php

class Card extends Model
{
    protected static string $table = 'cards';

    public static function byRarity(string $rarity): array
    {
        return static::where(['rarity' => $rarity]);
    }

    public static function allWithPagination(int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        return Database::query(
            "SELECT * FROM cards ORDER BY rarity DESC, name ASC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
    }

    public static function totalPages(int $perPage = 12): int
    {
        $total = static::count();
        return (int) ceil($total / $perPage);
    }

    // Couleur CSS selon la rareté
    public static function rarityColor(string $rarity): string
    {
        return match($rarity) {
            'legendary' => '#f59e0b',
            'epic'      => '#8b5cf6',
            'rare'      => '#3b82f6',
            default     => '#6b7280',
        };
    }

    // Label français
    public static function rarityLabel(string $rarity): string
    {
        return match($rarity) {
            'legendary' => 'Légendaire',
            'epic'      => 'Épique',
            'rare'      => 'Rare',
            default     => 'Commun',
        };
    }
}
