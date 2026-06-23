<?php

class Auction extends Model
{
    protected static string $table = 'auctions';

    // Enchères actives avec détails
    public static function getActive(int $excludeUserId = 0): array
    {
        return Database::query("
            SELECT a.*, c.name as card_name, c.rarity, c.type, c.image,
                   c.speed, c.power, c.handling, c.armor,
                   u.username as seller_name,
                   b.username as bidder_name
            FROM auctions a
            JOIN cards c ON a.card_id = c.id
            JOIN users u ON a.seller_id = u.id
            LEFT JOIN users b ON a.current_bidder_id = b.id
            WHERE a.status = 'active'
            AND a.ends_at > NOW()
            AND a.seller_id != ?
            ORDER BY a.ends_at ASC
        ", [$excludeUserId]);
    }

    // Met en enchère une carte (durée en heures)
    public static function create(array $data): string
    {
        return parent::create($data);
    }

    public static function listCard(int $sellerId, int $cardId, int $startPrice, int $durationHours = 24): bool
    {
        $userCard = Database::queryOne(
            "SELECT quantity FROM user_cards WHERE user_id = ? AND card_id = ?",
            [$sellerId, $cardId]
        );
        if (!$userCard || $userCard['quantity'] < 1) return false;

        // Retire la carte de la collection
        if ($userCard['quantity'] === 1) {
            Database::execute(
                "DELETE FROM user_cards WHERE user_id = ? AND card_id = ?",
                [$sellerId, $cardId]
            );
        } else {
            Database::execute(
                "UPDATE user_cards SET quantity = quantity - 1 WHERE user_id = ? AND card_id = ?",
                [$sellerId, $cardId]
            );
        }

        parent::create([
            'seller_id'   => $sellerId,
            'card_id'     => $cardId,
            'start_price' => $startPrice,
            'current_bid' => $startPrice,
            'min_increment' => max(10, (int)($startPrice * 0.1)),
            'status'      => 'active',
            'ends_at'     => date('Y-m-d H:i:s', strtotime("+{$durationHours} hours")),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    // Place une enchère
    public static function placeBid(int $auctionId, int $bidderId, int $amount): array
    {
        $auction = static::find($auctionId);
        if (!$auction || $auction['status'] !== 'active') {
            return ['success' => false, 'message' => 'Enchère introuvable ou terminée.'];
        }
        if (strtotime($auction['ends_at']) < time()) {
            return ['success' => false, 'message' => 'Cette enchère est expirée.'];
        }
        if ($auction['seller_id'] === $bidderId) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas enchérir sur votre propre carte.'];
        }

        $minBid = $auction['current_bid'] + $auction['min_increment'];
        if ($amount < $minBid) {
            return ['success' => false, 'message' => "Mise minimale : {$minBid} coins."];
        }

        $bidder = Database::queryOne("SELECT coins FROM users WHERE id = ?", [$bidderId]);
        if ($bidder['coins'] < $amount) {
            return ['success' => false, 'message' => 'Coins insuffisants.'];
        }

        Database::execute(
            "UPDATE auctions SET current_bid = ?, current_bidder_id = ? WHERE id = ?",
            [$amount, $bidderId, $auctionId]
        );

        return ['success' => true, 'message' => "Enchère placée à {$amount} coins !"];
    }

    // Finalise les enchères expirées (à appeler via cron ou à chaque page load)
    public static function closeExpired(): void
    {
        $expired = Database::query("
            SELECT * FROM auctions
            WHERE status = 'active' AND ends_at <= NOW()
        ");

        foreach ($expired as $auction) {
            if ($auction['current_bidder_id']) {
                // Il y a un gagnant → débite et donne la carte
                Database::execute(
                    "UPDATE users SET coins = coins - ? WHERE id = ?",
                    [$auction['current_bid'], $auction['current_bidder_id']]
                );
                Database::execute(
                    "UPDATE users SET coins = coins + ? WHERE id = ?",
                    [$auction['current_bid'], $auction['seller_id']]
                );

                $existing = Database::queryOne(
                    "SELECT id FROM user_cards WHERE user_id = ? AND card_id = ?",
                    [$auction['current_bidder_id'], $auction['card_id']]
                );
                if ($existing) {
                    Database::execute(
                        "UPDATE user_cards SET quantity = quantity + 1 WHERE user_id = ? AND card_id = ?",
                        [$auction['current_bidder_id'], $auction['card_id']]
                    );
                } else {
                    Database::execute(
                        "INSERT INTO user_cards (user_id, card_id, quantity) VALUES (?, ?, 1)",
                        [$auction['current_bidder_id'], $auction['card_id']]
                    );
                }
            } else {
                // Pas d'enchère → rend la carte au vendeur
                Database::execute(
                    "INSERT INTO user_cards (user_id, card_id, quantity) VALUES (?, ?, 1)
                     ON DUPLICATE KEY UPDATE quantity = quantity + 1",
                    [$auction['seller_id'], $auction['card_id']]
                );
            }

            Database::execute(
                "UPDATE auctions SET status = 'finished' WHERE id = ?",
                [$auction['id']]
            );
        }
    }
}
