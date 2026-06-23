<?php

class UserBooster extends Model
{
    protected static string $table = 'user_boosters';

    // Boosters d'un joueur avec les détails
    public static function getInventory(int $userId): array
    {
        return Database::query("
            SELECT b.*, ub.quantity, ub.purchased_at
            FROM user_boosters ub
            JOIN boosters b ON ub.booster_id = b.id
            WHERE ub.user_id = ?
            ORDER BY ub.purchased_at DESC
        ", [$userId]);
    }

    // Ajoute un booster à l'inventaire
    public static function addBooster(int $userId, int $boosterId): void
    {
        $existing = Database::queryOne(
            "SELECT id, quantity FROM user_boosters WHERE user_id = ? AND booster_id = ?",
            [$userId, $boosterId]
        );

        if ($existing) {
            Database::execute(
                "UPDATE user_boosters SET quantity = quantity + 1 WHERE id = ?",
                [$existing['id']]
            );
        } else {
            static::create([
                'user_id'      => $userId,
                'booster_id'   => $boosterId,
                'quantity'     => 1,
                'purchased_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // Consomme un booster (diminue la quantité ou supprime)
    public static function consume(int $userId, int $boosterId): bool
    {
        $existing = Database::queryOne(
            "SELECT id, quantity FROM user_boosters WHERE user_id = ? AND booster_id = ?",
            [$userId, $boosterId]
        );

        if (!$existing || $existing['quantity'] < 1) return false;

        if ($existing['quantity'] === 1) {
            Database::execute("DELETE FROM user_boosters WHERE id = ?", [$existing['id']]);
        } else {
            Database::execute(
                "UPDATE user_boosters SET quantity = quantity - 1 WHERE id = ?",
                [$existing['id']]
            );
        }

        return true;
    }
}
