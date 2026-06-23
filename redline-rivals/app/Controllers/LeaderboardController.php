<?php

class LeaderboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // Classement complet : tous les joueurs, triés par victoires puis coins
        $players = Database::query("
            SELECT u.id, u.username, u.role, u.coins,
                   (SELECT COUNT(*) FROM battles b WHERE b.winner_id = u.id) AS wins,
                   (SELECT COALESCE(SUM(uc.quantity), 0) FROM user_cards uc WHERE uc.user_id = u.id) AS cards
            FROM users u
            ORDER BY wins DESC, u.coins DESC, u.username ASC
        ");

        $this->render('leaderboard/index', [
            'title'   => 'Classement',
            'user'    => Auth::user(),
            'players' => $players,
            'meId'    => Auth::id(),
        ]);
    }
}
