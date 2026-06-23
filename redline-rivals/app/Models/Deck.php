<?php

class Deck extends Model
{
    protected static string $table = 'decks';

    // Decks d'un joueur avec le nombre de cartes
    public static function getByUser(int $userId): array
    {
        return Database::query("
            SELECT d.*, COUNT(dc.card_id) as card_count
            FROM decks d
            LEFT JOIN deck_cards dc ON d.id = dc.deck_id
            WHERE d.user_id = ?
            GROUP BY d.id
            ORDER BY d.created_at DESC
        ", [$userId]);
    }

    // Détail d'un deck avec ses cartes complètes
    public static function getWithCards(int $deckId): ?array
    {
        $deck = static::find($deckId);
        if (!$deck) return null;

        $deck['cards'] = Database::query("
            SELECT c.*
            FROM deck_cards dc
            JOIN cards c ON dc.card_id = c.id
            WHERE dc.deck_id = ?
        ", [$deckId]);

        return $deck;
    }

    // Ajoute une carte au deck
    public static function addCard(int $deckId, int $cardId): bool
    {
        $existing = Database::queryOne(
            "SELECT id FROM deck_cards WHERE deck_id = ? AND card_id = ?",
            [$deckId, $cardId]
        );
        if ($existing) return false;

        Database::execute(
            "INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)",
            [$deckId, $cardId]
        );
        return true;
    }

    // Retire une carte du deck
    public static function removeCard(int $deckId, int $cardId): void
    {
        Database::execute(
            "DELETE FROM deck_cards WHERE deck_id = ? AND card_id = ?",
            [$deckId, $cardId]
        );
    }

    // Compte les cartes d'un deck
    public static function cardCount(int $deckId): int
    {
        $result = Database::queryOne(
            "SELECT COUNT(*) as total FROM deck_cards WHERE deck_id = ?",
            [$deckId]
        );
        return (int) ($result['total'] ?? 0);
    }
}
