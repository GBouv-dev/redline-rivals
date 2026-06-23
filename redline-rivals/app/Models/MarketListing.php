<?php

class MarketListing extends Model
{
    protected static string $table = 'market_listings';

    // Listings actifs avec détails carte + vendeur
    public static function getActive(int $excludeUserId = 0): array
    {
        return Database::query("
            SELECT ml.*, c.name as card_name, c.rarity, c.type,
                   c.speed, c.power, c.handling, c.armor, c.image,
                   u.username as seller_name
            FROM market_listings ml
            JOIN cards c ON ml.card_id = c.id
            JOIN users u ON ml.seller_id = u.id
            WHERE ml.status = 'active'
            AND ml.seller_id != ?
            ORDER BY ml.created_at DESC
        ", [$excludeUserId]);
    }

    // Listings d'un vendeur
    public static function getBySeller(int $userId): array
    {
        return Database::query("
            SELECT ml.*, c.name as card_name, c.rarity, c.image
            FROM market_listings ml
            JOIN cards c ON ml.card_id = c.id
            WHERE ml.seller_id = ?
            ORDER BY ml.created_at DESC
        ", [$userId]);
    }

    // Met en vente une carte
    public static function list(int $sellerId, int $cardId, int $price, int $quantity = 1): bool
    {
        // Vérifie que le vendeur possède assez de cartes
        $userCard = Database::queryOne(
            "SELECT quantity FROM user_cards WHERE user_id = ? AND card_id = ?",
            [$sellerId, $cardId]
        );
        if (!$userCard || $userCard['quantity'] < $quantity) return false;

        // Retire les cartes de la collection
        if ($userCard['quantity'] === $quantity) {
            Database::execute(
                "DELETE FROM user_cards WHERE user_id = ? AND card_id = ?",
                [$sellerId, $cardId]
            );
        } else {
            Database::execute(
                "UPDATE user_cards SET quantity = quantity - ? WHERE user_id = ? AND card_id = ?",
                [$quantity, $sellerId, $cardId]
            );
        }

        static::create([
            'seller_id'  => $sellerId,
            'card_id'    => $cardId,
            'price'      => $price,
            'quantity'   => $quantity,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    // Achète un listing
    public static function buy(int $listingId, int $buyerId): array
    {
        $listing = Database::queryOne("
            SELECT ml.*, c.name as card_name
            FROM market_listings ml
            JOIN cards c ON ml.card_id = c.id
            WHERE ml.id = ? AND ml.status = 'active'
        ", [$listingId]);

        if (!$listing) return ['success' => false, 'message' => 'Annonce introuvable.'];
        if ($listing['seller_id'] === $buyerId) return ['success' => false, 'message' => 'Vous ne pouvez pas acheter votre propre annonce.'];

        $buyer = Database::queryOne("SELECT coins FROM users WHERE id = ?", [$buyerId]);
        if ($buyer['coins'] < $listing['price']) return ['success' => false, 'message' => 'Coins insuffisants.'];

        // Transaction
        Database::execute("UPDATE users SET coins = coins - ? WHERE id = ?", [$listing['price'], $buyerId]);
        Database::execute("UPDATE users SET coins = coins + ? WHERE id = ?", [$listing['price'], $listing['seller_id']]);

        // Ajoute la carte à la collection de l'acheteur
        $existing = Database::queryOne(
            "SELECT id FROM user_cards WHERE user_id = ? AND card_id = ?",
            [$buyerId, $listing['card_id']]
        );
        if ($existing) {
            Database::execute(
                "UPDATE user_cards SET quantity = quantity + ? WHERE user_id = ? AND card_id = ?",
                [$listing['quantity'], $buyerId, $listing['card_id']]
            );
        } else {
            Database::execute(
                "INSERT INTO user_cards (user_id, card_id, quantity) VALUES (?, ?, ?)",
                [$buyerId, $listing['card_id'], $listing['quantity']]
            );
        }

        // Marque comme vendu
        Database::execute("
            UPDATE market_listings SET status = 'sold', buyer_id = ?, sold_at = ? WHERE id = ?
        ", [$buyerId, date('Y-m-d H:i:s'), $listingId]);

        return ['success' => true, 'message' => "Carte \"{$listing['card_name']}\" achetée !"];
    }

    // Annule un listing et rend la carte au vendeur
    public static function cancel(int $listingId, int $userId): bool
    {
        $listing = Database::queryOne(
            "SELECT * FROM market_listings WHERE id = ? AND seller_id = ? AND status = 'active'",
            [$listingId, $userId]
        );
        if (!$listing) return false;

        Database::execute(
            "UPDATE market_listings SET status = 'cancelled' WHERE id = ?",
            [$listingId]
        );

        $existing = Database::queryOne(
            "SELECT id FROM user_cards WHERE user_id = ? AND card_id = ?",
            [$userId, $listing['card_id']]
        );
        if ($existing) {
            Database::execute(
                "UPDATE user_cards SET quantity = quantity + ? WHERE user_id = ? AND card_id = ?",
                [$listing['quantity'], $userId, $listing['card_id']]
            );
        } else {
            Database::execute(
                "INSERT INTO user_cards (user_id, card_id, quantity) VALUES (?, ?, ?)",
                [$userId, $listing['card_id'], $listing['quantity']]
            );
        }

        return true;
    }
}
