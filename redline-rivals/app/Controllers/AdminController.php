<?php

require_once ROOT . '/app/Models/User.php';
require_once ROOT . '/app/Models/Card.php';
require_once ROOT . '/app/Models/Booster.php';
require_once ROOT . '/app/Models/Deck.php';
require_once ROOT . '/app/Models/Battle.php';
require_once ROOT . '/app/Models/Auction.php';
require_once ROOT . '/app/Models/MarketListing.php';

class AdminController extends Controller
{
    // Images utilisées en dur par le site (fonds) — à ne jamais supprimer
    private const PROTECTED_IMAGES = [
        '2016-Apollo-Arrow-002-1080.webp',
        '2023-Porsche-911-GT3-RS-003-1080.webp',
    ];

    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/index', [
            'title'           => 'Admin',
            'user'            => Auth::user(),
            // KPIs
            'totalUsers'      => User::count(),
            'totalAdmins'     => User::count(['role' => 'admin']),
            'totalCards'      => Card::count(),
            'totalDecks'      => Deck::count(),
            'totalBattles'    => Battle::count(),
            'finishedBattles' => Battle::count(['status' => 'finished']),
            'activeBattles'   => Battle::count(['status' => 'active']),
            'activeAuctions'  => Auction::count(['status' => 'active']),
            'activeListings'  => MarketListing::count(['status' => 'active']),
            'soldListings'    => MarketListing::count(['status' => 'sold']),
            // Économie
            'totalCoins'      => (int) (Database::queryOne("SELECT COALESCE(SUM(coins),0) t FROM users")['t'] ?? 0),
            'ownedCards'      => (int) (Database::queryOne("SELECT COALESCE(SUM(quantity),0) t FROM user_cards")['t'] ?? 0),
            'ownedBoosters'   => (int) (Database::queryOne("SELECT COALESCE(SUM(quantity),0) t FROM user_boosters")['t'] ?? 0),
            // Distributions par rareté
            'catalogByRarity' => $this->rarityMap("SELECT rarity, COUNT(*) n FROM cards GROUP BY rarity"),
            'ownedByRarity'   => $this->rarityMap("SELECT c.rarity, COALESCE(SUM(uc.quantity),0) n FROM user_cards uc JOIN cards c ON uc.card_id = c.id GROUP BY c.rarity"),
            // Tables
            'recentUsers'     => Database::query("SELECT id, username, email, role, coins, created_at FROM users ORDER BY created_at DESC LIMIT 8"),
            'allPlayers'      => User::ranking(),
            'meId'            => Auth::id(),
        ]);
    }

    // Normalise un GROUP BY rarity en map ordonnée S→C
    private function rarityMap(string $sql): array
    {
        $map = ['legendary' => 0, 'epic' => 0, 'rare' => 0, 'common' => 0];
        foreach (Database::query($sql) as $r) {
            $map[$r['rarity']] = (int) $r['n'];
        }
        return $map;
    }

    // ───────── Gestion des cartes (CRUD) ─────────

    public function cards(): void
    {
        $this->requireAdmin();
        $this->render('admin/cards', [
            'title'        => 'Gestion des cartes',
            'user'         => Auth::user(),
            'cards'        => Database::query("SELECT * FROM cards ORDER BY rarity DESC, name ASC"),
            'imageOptions' => $this->imageOptions(),
        ]);
    }

    public function createCard(): void
    {
        $this->requireAdmin();
        $data = $this->cardInput();
        if ($data['name'] === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Le nom de la carte est requis.'];
            $this->redirect('/admin/cards');
        }
        [$st, $val] = $this->handleImageUpload();
        if ($st === 'error') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $val];
            $this->redirect('/admin/cards');
        }
        if ($st === 'ok') {
            $data['image'] = $val;
        }
        Card::create($data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => "Carte « {$data['name']} » créée."];
        $this->redirect('/admin/cards');
    }

    public function editCard(string $id): void
    {
        $this->requireAdmin();
        $card = Card::find((int) $id);
        if (!$card) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Carte introuvable.'];
            $this->redirect('/admin/cards');
        }
        $this->render('admin/card_edit', [
            'title'        => 'Modifier — ' . $card['name'],
            'user'         => Auth::user(),
            'card'         => $card,
            'imageOptions' => $this->imageOptions(),
        ]);
    }

    public function updateCard(string $id): void
    {
        $this->requireAdmin();
        $card = Card::find((int) $id);
        if (!$card) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Carte introuvable.'];
            $this->redirect('/admin/cards');
        }
        $data = $this->cardInput();
        if ($data['name'] === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Le nom de la carte est requis.'];
            $this->redirect('/admin/cards/' . (int) $id . '/edit');
        }
        [$st, $val] = $this->handleImageUpload();
        if ($st === 'error') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $val];
            $this->redirect('/admin/cards/' . (int) $id . '/edit');
        }
        if ($st === 'ok') {
            $data['image'] = $val;
        }
        Card::update((int) $id, $data);
        if (!empty($card['image']) && $card['image'] !== ($data['image'] ?? null)) {
            $this->maybeDeleteImage($card['image']);
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => "Carte « {$data['name']} » mise à jour."];
        $this->redirect('/admin/cards');
    }

    public function deleteCard(string $id): void
    {
        $this->requireAdmin();
        $card = Card::find((int) $id);
        if ($card) {
            Card::delete((int) $id);
            $this->maybeDeleteImage($card['image'] ?? null);
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Carte « {$card['name']} » supprimée."];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Carte introuvable.'];
        }
        $this->redirect('/admin/cards');
    }

    // Lit et valide les champs d'une carte depuis $_POST (stockage brut, échappé à l'affichage)
    private function cardInput(): array
    {
        $post     = $_POST;
        $rarities = ['common', 'rare', 'epic', 'legendary'];
        $types    = ['sport', 'muscle', 'tuner', 'hypercar', 'truck'];
        $clamp    = fn($v) => max(1, min(100, (int) $v));
        $rarity   = $post['rarity'] ?? 'common';
        $type     = $post['type'] ?? 'sport';
        $finishes = ['classic', 'semiholo', 'holo', 'fullart'];
        $finish   = $post['finish'] ?? 'classic';
        $image    = trim($post['image'] ?? '');
        $opts     = $this->imageOptions();

        return [
            'name'        => trim($post['name'] ?? ''),
            'description' => trim($post['description'] ?? ''),
            'rarity'      => in_array($rarity, $rarities, true) ? $rarity : 'common',
            'type'        => in_array($type, $types, true) ? $type : 'sport',
            'speed'       => $clamp($post['speed'] ?? 50),
            'power'       => $clamp($post['power'] ?? 50),
            'handling'    => $clamp($post['handling'] ?? 50),
            'armor'       => $clamp($post['armor'] ?? 50),
            'image'       => ($image !== '' && in_array($image, $opts, true)) ? $image : null,
            'finish'      => in_array($finish, $finishes, true) ? $finish : 'classic',
        ];
    }

    // Liste les fichiers image disponibles dans le dossier des cartes
    private function imageOptions(): array
    {
        $dir   = ROOT . '/public/assets/img/cards/';
        $files = array_merge(
            glob($dir . '*.webp') ?: [],
            glob($dir . '*.jpg') ?: [],
            glob($dir . '*.jpeg') ?: [],
            glob($dir . '*.png') ?: []
        );
        $names = array_map('basename', $files);
        sort($names);
        return $names;
    }

    // Gère un éventuel upload d'image — retourne [statut, valeur] : 'none' | 'ok' | 'error'
    private function handleImageUpload(): array
    {
        $f = $_FILES['image_file'] ?? null;
        if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['none', null];
        }
        if ($f['error'] !== UPLOAD_ERR_OK) {
            return ['error', "Échec de l'upload (code {$f['error']})."];
        }
        if (!is_uploaded_file($f['tmp_name'])) {
            return ['error', 'Fichier invalide.'];
        }
        if ($f['size'] > 3 * 1024 * 1024) {
            return ['error', 'Image trop lourde (max 3 Mo).'];
        }

        // Validation par le contenu réel (pas par le nom envoyé)
        $info = @getimagesize($f['tmp_name']);
        $map  = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
        if ($info === false || !isset($map[$info[2]])) {
            return ['error', 'Format non supporté (JPG, PNG ou WebP uniquement).'];
        }
        $ext = $map[$info[2]];

        // Nom de fichier assaini, extension forcée d'après le type détecté
        $base = preg_replace('/[^A-Za-z0-9_-]+/', '-', pathinfo($f['name'], PATHINFO_FILENAME));
        $base = trim((string) $base, '-');
        if ($base === '') $base = 'carte';
        $base = substr($base, 0, 60);

        $dir      = ROOT . '/public/assets/img/cards/';
        $filename = $base . '.' . $ext;
        $i = 1;
        while (file_exists($dir . $filename)) {
            $filename = $base . '-' . $i . '.' . $ext;
            $i++;
        }
        if (!@move_uploaded_file($f['tmp_name'], $dir . $filename)) {
            return ['error', "Impossible d'enregistrer l'image sur le serveur."];
        }
        return ['ok', $filename];
    }

    // Supprime le fichier image s'il est devenu orphelin (et non protégé)
    private function maybeDeleteImage(?string $filename): void
    {
        if (!$filename) return;
        $filename = basename($filename);
        if (in_array($filename, self::PROTECTED_IMAGES, true)) return;

        $stillUsed = (int) (Database::queryOne("SELECT COUNT(*) AS c FROM cards WHERE image = ?", [$filename])['c'] ?? 0);
        if ($stillUsed > 0) return;

        $path = ROOT . '/public/assets/img/cards/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
