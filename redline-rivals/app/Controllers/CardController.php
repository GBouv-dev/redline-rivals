<?php

require_once ROOT . '/app/Models/Card.php';
require_once ROOT . '/app/Models/UserCard.php';

class CardController extends Controller
{
    // Liste toutes les cartes (encyclopédie)
    public function index(): void
    {
        $this->requireAuth();
        $page  = max(1, (int) $this->query('page', 1));
        $cards = Card::allWithPagination($page);
        $total = Card::totalPages();

        $this->render('cards/index', [
            'title'      => 'Toutes les cartes',
            'cards'      => $cards,
            'page'       => $page,
            'totalPages' => $total,
            'user'       => Auth::user(),
        ]);
    }

    // Détail d'une carte
    public function show(string $id): void
    {
        $this->requireAuth();
        $card = Card::find((int) $id);

        if (!$card) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Carte introuvable.'];
            $this->redirect('/cards');
        }

        $uc = Database::queryOne(
            "SELECT finish, finishes_owned, quantity FROM user_cards WHERE user_id = ? AND card_id = ?",
            [Auth::id(), (int) $id]
        );

        $this->render('cards/show', [
            'title'         => $card['name'],
            'card'          => $card,
            'owned'         => $uc !== null,
            'ownedFinish'   => $uc['finish'] ?? null,
            'ownedQty'      => (int) ($uc['quantity'] ?? 0),
            'finishesOwned' => ($uc && $uc['finishes_owned'] !== '') ? explode(',', $uc['finishes_owned']) : [],
            'user'          => Auth::user(),
        ]);
    }

    // Collection du joueur connecté
    public function collection(): void
    {
        $this->requireAuth();
        $cards = UserCard::getCollection(Auth::id());

        $this->render('cards/collection', [
            'title' => 'Ma collection',
            'cards' => $cards,
            'user'  => Auth::user(),
        ]);
    }

    // Choisit la finition affichée pour une carte possédée
    public function setFinish(string $id): void
    {
        $this->requireAuth();
        $cardId = (int) $id;
        $finish = $_POST['finish'] ?? '';

        $row = Database::queryOne(
            "SELECT id, finishes_owned FROM user_cards WHERE user_id = ? AND card_id = ?",
            [Auth::id(), $cardId]
        );
        $owned = ($row && $row['finishes_owned'] !== '') ? explode(',', $row['finishes_owned']) : [];

        if ($row && in_array($finish, $owned, true)) {
            Database::execute("UPDATE user_cards SET finish = ? WHERE id = ?", [$finish, $row['id']]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Finition affichée mise à jour.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => "Cette finition n'est pas disponible pour cette carte."];
        }

        // Reste sur la page d'origine (collection ou fiche carte)
        $back = '/cards/collection';
        $ref  = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH);
        if (is_string($ref) && str_starts_with($ref, '/cards/')) {
            $back = $ref;
        }
        $this->redirect($back);
    }
}
