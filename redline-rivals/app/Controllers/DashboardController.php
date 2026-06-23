<?php

require_once ROOT . '/app/Models/UserCard.php';
require_once ROOT . '/app/Models/UserBooster.php';
require_once ROOT . '/app/Models/Deck.php';
require_once ROOT . '/app/Models/Battle.php';
require_once ROOT . '/app/Models/Booster.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = Auth::id();

        $rarityRows = Database::query(
            "SELECT c.rarity, COUNT(*) as n FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_id = ? GROUP BY c.rarity",
            [$userId]
        );
        $rarityBreakdown = ['legendary' => 0, 'epic' => 0, 'rare' => 0, 'common' => 0];
        foreach ($rarityRows as $r) {
            $rarityBreakdown[$r['rarity']] = (int) $r['n'];
        }

        $this->render('dashboard/index', [
            'title'        => 'Dashboard',
            'user'         => Auth::user(),
            'cardCount'    => Database::queryOne("SELECT COUNT(*) as total FROM user_cards WHERE user_id = ?", [$userId])['total'] ?? 0,
            'boosterCount' => Database::queryOne("SELECT COALESCE(SUM(quantity),0) as total FROM user_boosters WHERE user_id = ?", [$userId])['total'] ?? 0,
            'deckCount'    => Database::queryOne("SELECT COUNT(*) as total FROM decks WHERE user_id = ?", [$userId])['total'] ?? 0,
            'battleWins'   => Database::queryOne("SELECT COUNT(*) as total FROM battles WHERE winner_id = ?", [$userId])['total'] ?? 0,
            'myCards'      => Database::query("SELECT c.*, uc.quantity, uc.finish FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_id = ? ORDER BY c.rarity DESC LIMIT 4", [$userId]),
            'boosters'     => Booster::allActive(),
            'rarityBreakdown' => $rarityBreakdown,
        ]);
    }
}