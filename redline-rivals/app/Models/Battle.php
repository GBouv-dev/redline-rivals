<?php

class Battle extends Model
{
    protected static string $table = 'battles';

    const STATUS_WAITING  = 'waiting';
    const STATUS_ACTIVE   = 'active';
    const STATUS_FINISHED = 'finished';

    // Combats en attente (pas encore rejoints)
    public static function getWaiting(int $excludeUserId): array
    {
        return Database::query("
            SELECT b.*, u.username as player1_name, d.name as deck1_name
            FROM battles b
            JOIN users u ON b.player1_id = u.id
            JOIN decks d ON b.deck1_id = d.id
            WHERE b.status = 'waiting'
            AND b.player1_id != ?
            ORDER BY b.created_at DESC
        ", [$excludeUserId]);
    }

    // Combats actifs d'un joueur
    public static function getActiveBattles(int $userId): array
    {
        return Database::query("
            SELECT b.*,
                u1.username as player1_name,
                u2.username as player2_name
            FROM battles b
            JOIN users u1 ON b.player1_id = u1.id
            LEFT JOIN users u2 ON b.player2_id = u2.id
            WHERE (b.player1_id = ? OR b.player2_id = ?)
            AND b.status IN ('waiting', 'active')
            ORDER BY b.created_at DESC
        ", [$userId, $userId]);
    }

    // Crée un nouveau combat
    public static function createBattle(int $userId, int $deckId, array $cardIds): int
    {
        shuffle($cardIds);
        $gameState = json_encode([
            'p1Hand'      => $cardIds,
            'p2Hand'      => [],
            'p1Played'    => null,
            'p2Played'    => null,
            'rounds'      => [],
            'currentTurn' => 1,
        ]);

        return (int) static::create([
            'player1_id' => $userId,
            'deck1_id'   => $deckId,
            'status'     => self::STATUS_WAITING,
            'game_state' => $gameState,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Rejoindre un combat
    public static function joinBattle(int $battleId, int $userId, int $deckId, array $cardIds): bool
    {
        $battle = static::find($battleId);
        if (!$battle || $battle['status'] !== self::STATUS_WAITING) return false;
        if ($battle['player1_id'] === $userId) return false;

        shuffle($cardIds);
        $gs = json_decode($battle['game_state'], true);
        $gs['p2Hand'] = $cardIds;

        Database::execute("
            UPDATE battles SET
                player2_id = ?, deck2_id = ?,
                status = 'active',
                game_state = ?
            WHERE id = ?
        ", [$userId, $deckId, json_encode($gs), $battleId]);

        return true;
    }

    // Joue une carte (stocke le choix du joueur)
    public static function playCard(int $battleId, int $userId, int $cardId): array
    {
        $battle = static::find($battleId);
        if (!$battle || $battle['status'] !== self::STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'Combat invalide.'];
        }

        $gs       = json_decode($battle['game_state'], true);
        $isPlayer1 = $battle['player1_id'] === $userId;
        $key       = $isPlayer1 ? 'p1Played' : 'p2Played';
        $handKey   = $isPlayer1 ? 'p1Hand' : 'p2Hand';

        if ($gs[$key] !== null) {
            return ['success' => false, 'message' => 'Vous avez déjà joué ce tour.'];
        }
        if (!in_array($cardId, $gs[$handKey])) {
            return ['success' => false, 'message' => 'Carte introuvable dans votre main.'];
        }

        $gs[$key] = $cardId;
        $gs[$handKey] = array_values(array_filter($gs[$handKey], fn($id) => $id !== $cardId));

        // Si les deux joueurs ont joué → résoudre le round
        if ($gs['p1Played'] !== null && $gs['p2Played'] !== null) {
            $gs = static::resolveRound($battle, $gs);
        }

        Database::execute(
            "UPDATE battles SET game_state = ? WHERE id = ?",
            [json_encode($gs), $battleId]
        );

        return ['success' => true, 'gameState' => $gs];
    }

    // Résout un round et détermine le gagnant de la manche
    private static function resolveRound(array $battle, array $gs): array
    {
        $card1 = Database::queryOne("SELECT * FROM cards WHERE id = ?", [$gs['p1Played']]);
        $card2 = Database::queryOne("SELECT * FROM cards WHERE id = ?", [$gs['p2Played']]);

        $score1 = $card1['speed'] + $card1['power'] + $card1['handling'] + $card1['armor'];
        $score2 = $card2['speed'] + $card2['power'] + $card2['handling'] + $card2['armor'];

        $roundWinner = match(true) {
            $score1 > $score2 => 'p1',
            $score2 > $score1 => 'p2',
            default           => 'draw',
        };

        $gs['rounds'][] = [
            'turn'       => $gs['currentTurn'],
            'p1CardId'   => $gs['p1Played'],
            'p2CardId'   => $gs['p2Played'],
            'p1Score'    => $score1,
            'p2Score'    => $score2,
            'winner'     => $roundWinner,
        ];

        $gs['p1Played']    = null;
        $gs['p2Played']    = null;
        $gs['currentTurn'] = ($gs['currentTurn'] ?? 1) + 1;

        // Vérifie si le combat est terminé (plus de cartes ou 5 rounds)
        if (empty($gs['p1Hand']) || empty($gs['p2Hand']) || count($gs['rounds']) >= 5) {
            $p1Wins = count(array_filter($gs['rounds'], fn($r) => $r['winner'] === 'p1'));
            $p2Wins = count(array_filter($gs['rounds'], fn($r) => $r['winner'] === 'p2'));

            $winnerId = match(true) {
                $p1Wins > $p2Wins => $battle['player1_id'],
                $p2Wins > $p1Wins => $battle['player2_id'],
                default           => null,
            };

            Database::execute("
                UPDATE battles SET status = 'finished', winner_id = ?, finished_at = ?
                WHERE id = ?
            ", [$winnerId, date('Y-m-d H:i:s'), $battle['id']]);

            $gs['finished'] = true;
            $gs['winnerId'] = $winnerId;

            // Récompense le gagnant
            if ($winnerId) {
                Database::execute(
                    "UPDATE users SET coins = coins + 50 WHERE id = ?",
                    [$winnerId]
                );
            }
        }

        return $gs;
    }
}
