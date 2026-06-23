<?php

require_once ROOT . '/app/Models/User.php';

class ProfileController extends Controller
{
    // [label, description, clé de stat, objectif, récompense, icône]
    private const QUESTS = [
        'q_collect3'  => ['Démarre ta collection', 'Posséder 3 cartes uniques',        'uniqueCards',      3, 50,  '🃏'],
        'q_collect10' => ['Garage en expansion',   'Posséder 10 cartes uniques',       'uniqueCards',     10, 150, '🚗'],
        'q_win3'      => ['Pilote victorieux',      'Gagner 3 combats',                 'wins',             3, 200, '🏁'],
        'q_decks2'    => ['Maître tacticien',       'Créer 2 decks',                    'decks',            2, 100, '📋'],
        'q_holo'      => ['Chasseur de reflets',    'Obtenir une carte holo ou mieux',  'holoPlus',         1, 250, '✨'],
        'q_finishes'  => ['Collectionneur de finitions', 'Obtenir les 4 finitions',     'distinctFinishes', 4, 300, '🌈'],
    ];

    public function index(): void
    {
        $this->requireAuth();
        $userId = Auth::id();
        $user   = Database::queryOne("SELECT * FROM users WHERE id = ?", [$userId]);
        $stats  = $this->computeStats($userId, (int) $user['coins']);

        // Quêtes : progression calculée + statut réclamé
        $claimedKeys = array_column(
            Database::query("SELECT quest_key FROM user_quests WHERE user_id = ?", [$userId]),
            'quest_key'
        );
        $quests = [];
        foreach (self::QUESTS as $key => $q) {
            [$label, $desc, $statKey, $target, $reward, $icon] = $q;
            $current = (int) ($stats[$statKey] ?? 0);
            $done    = $current >= $target;
            $quests[] = [
                'key' => $key, 'label' => $label, 'desc' => $desc, 'icon' => $icon,
                'current' => min($current, $target), 'target' => $target, 'reward' => $reward,
                'done' => $done, 'claimed' => in_array($key, $claimedKeys, true),
            ];
        }

        $this->render('profile/index', [
            'title'   => 'Profil de ' . $user['username'],
            'user'    => $user,
            'stats'   => $stats,
            'badges'  => $this->badges($stats),
            'quests'  => $quests,
        ]);
    }

    public function claim(string $key): void
    {
        $this->requireAuth();
        $userId = Auth::id();

        if (!isset(self::QUESTS[$key])) {
            $this->redirect('/profil');
        }
        [, , $statKey, $target, $reward] = self::QUESTS[$key];

        $already = Database::queryOne("SELECT id FROM user_quests WHERE user_id = ? AND quest_key = ?", [$userId, $key]);
        $coins   = (int) Database::queryOne("SELECT coins FROM users WHERE id = ?", [$userId])['coins'];
        $stats   = $this->computeStats($userId, $coins);

        if ($already) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Récompense déjà réclamée.'];
        } elseif ((int) ($stats[$statKey] ?? 0) < $target) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Quête non terminée.'];
        } else {
            Database::execute("INSERT INTO user_quests (user_id, quest_key, claimed_at) VALUES (?, ?, ?)", [$userId, $key, date('Y-m-d H:i:s')]);
            Database::execute("UPDATE users SET coins = coins + ? WHERE id = ?", [$reward, $userId]);
            Auth::login(Database::queryOne("SELECT * FROM users WHERE id = ?", [$userId]));
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Récompense réclamée : +{$reward} coins !"];
        }
        $this->redirect('/profil');
    }

    private function computeStats(int $userId, int $coins): array
    {
        $one = fn($sql, $p = []) => (int) (Database::queryOne($sql, $p)['n'] ?? 0);

        $distinctFinishes = 0;
        foreach (['classic', 'semiholo', 'holo', 'fullart'] as $f) {
            if (Database::queryOne("SELECT 1 AS n FROM user_cards WHERE user_id = ? AND FIND_IN_SET(?, finishes_owned) LIMIT 1", [$userId, $f])) {
                $distinctFinishes++;
            }
        }
        $hasHolo    = $one("SELECT COUNT(*) n FROM user_cards WHERE user_id = ? AND FIND_IN_SET('holo', finishes_owned)", [$userId]) > 0;
        $hasFullart = $one("SELECT COUNT(*) n FROM user_cards WHERE user_id = ? AND FIND_IN_SET('fullart', finishes_owned)", [$userId]) > 0;

        return [
            'coins'            => $coins,
            'uniqueCards'      => $one("SELECT COUNT(*) n FROM user_cards WHERE user_id = ?", [$userId]),
            'totalCards'       => $one("SELECT COALESCE(SUM(quantity),0) n FROM user_cards WHERE user_id = ?", [$userId]),
            'catalogTotal'     => $one("SELECT COUNT(*) n FROM cards"),
            'wins'             => $one("SELECT COUNT(*) n FROM battles WHERE winner_id = ?", [$userId]),
            'battlesPlayed'    => $one("SELECT COUNT(*) n FROM battles WHERE (player1_id = ? OR player2_id = ?) AND status = 'finished'", [$userId, $userId]),
            'decks'            => $one("SELECT COUNT(*) n FROM decks WHERE user_id = ?", [$userId]),
            'legendaries'      => $one("SELECT COUNT(*) n FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_id = ? AND c.rarity = 'legendary'", [$userId]),
            'hasHolo'          => $hasHolo,
            'hasFullart'       => $hasFullart,
            'holoPlus'         => ($hasHolo || $hasFullart) ? 1 : 0,
            'distinctFinishes' => $distinctFinishes,
        ];
    }

    private function badges(array $s): array
    {
        $defs = [
            ['🃏', 'Premiers tours de roue', 'Posséder une carte',            $s['uniqueCards'] >= 1],
            ['🗃️', 'Collectionneur',         'Posséder 10 cartes uniques',    $s['uniqueCards'] >= 10],
            ['📚', 'Encyclopédiste',          'Posséder toutes les cartes',    $s['catalogTotal'] > 0 && $s['uniqueCards'] >= $s['catalogTotal']],
            ['🏆', 'Première victoire',        'Gagner un combat',              $s['wins'] >= 1],
            ['👑', 'Champion',                'Gagner 10 combats',             $s['wins'] >= 10],
            ['📋', 'Stratège',                'Créer un deck',                 $s['decks'] >= 1],
            ['✨', 'Reflet holographique',     'Obtenir une carte holo',        $s['hasHolo']],
            ['🖼️', 'Pièce de collection',      'Obtenir une carte full art',    $s['hasFullart']],
            ['🌟', 'Légende',                 'Posséder une carte légendaire', $s['legendaries'] >= 1],
            ['💰', 'Fortune',                 'Atteindre 1000 coins',          $s['coins'] >= 1000],
            ['💎', 'Magnat',                  'Atteindre 5000 coins',          $s['coins'] >= 5000],
            ['🌈', 'Arc-en-ciel',             'Obtenir les 4 finitions',       $s['distinctFinishes'] >= 4],
        ];
        return array_map(fn($b) => ['icon' => $b[0], 'name' => $b[1], 'desc' => $b[2], 'earned' => (bool) $b[3]], $defs);
    }
}
