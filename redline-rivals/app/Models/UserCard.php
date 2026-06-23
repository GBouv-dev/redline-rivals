<?php

class UserCard extends Model
{
    protected static string $table = 'user_cards';

    // Collection complète d'un joueur avec les détails des cartes
    public static function getCollection(int $userId): array
    {
        return Database::query("
            SELECT c.*, uc.quantity, uc.obtained_at, uc.finish, uc.finishes_owned
            FROM user_cards uc
            JOIN cards c ON uc.card_id = c.id
            WHERE uc.user_id = ?
            ORDER BY c.rarity DESC, c.name ASC
        ", [$userId]);
    }

    // Ordre des finitions (pour la montée en gamme)
    private const FINISH_TIERS = ['classic' => 0, 'semiholo' => 1, 'holo' => 2, 'fullart' => 3];

    // Tire une finition au sort, indépendamment de la rareté
    public static function rollFinish(): string
    {
        $r = mt_rand(1, 1000);
        if ($r <= 20)  return 'fullart';   // 2 %
        if ($r <= 80)  return 'holo';      // 6 %
        if ($r <= 250) return 'semiholo';  // 17 %
        return 'classic';                  // 75 %
    }

    // Ajoute une carte à la collection (incrémente la quantité, conserve la meilleure finition obtenue)
    public static function addCard(int $userId, int $cardId, string $finish = 'classic'): void
    {
        $existing = Database::queryOne(
            "SELECT id, finish, finishes_owned FROM user_cards WHERE user_id = ? AND card_id = ?",
            [$userId, $cardId]
        );

        if ($existing) {
            // Cumule la finition dans la liste des finitions obtenues
            $owned = $existing['finishes_owned'] !== '' ? explode(',', $existing['finishes_owned']) : [];
            if (!in_array($finish, $owned, true)) {
                $owned[] = $finish;
            }
            // La finition affichée par défaut = la meilleure obtenue
            $newTier = self::FINISH_TIERS[$finish] ?? 0;
            $oldTier = self::FINISH_TIERS[$existing['finish']] ?? 0;
            $display = $newTier > $oldTier ? $finish : $existing['finish'];
            Database::execute(
                "UPDATE user_cards SET quantity = quantity + 1, finish = ?, finishes_owned = ? WHERE id = ?",
                [$display, implode(',', $owned), $existing['id']]
            );
        } else {
            static::create([
                'user_id'        => $userId,
                'card_id'        => $cardId,
                'quantity'       => 1,
                'finish'         => $finish,
                'finishes_owned' => $finish,
                'obtained_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // Vérifie si un joueur possède une carte
    public static function owns(int $userId, int $cardId): bool
    {
        return static::findBy(['user_id' => $userId, 'card_id' => $cardId]) !== null;
    }
}
