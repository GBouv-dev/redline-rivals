<?php

require_once ROOT . '/app/Models/Battle.php';
require_once ROOT . '/app/Models/Deck.php';
require_once ROOT . '/app/Models/Card.php';

class BattleController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = Auth::id();

        $this->render('arena/index', [
            'title'         => 'Arène',
            'user'          => Auth::user(),
            'waitingBattles'=> Battle::getWaiting($userId),
            'myBattles'     => Battle::getActiveBattles($userId),
            'myDecks'       => Deck::getByUser($userId),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $deckId = (int)$this->input('deck_id');
        $deck   = Deck::getWithCards($deckId);

        if (!$deck || $deck['user_id'] !== Auth::id()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Deck invalide.'];
            $this->redirect('/arena');
        }

        if (count($deck['cards']) < 3) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Il faut au moins 3 cartes dans le deck.'];
            $this->redirect('/arena');
        }

        $cardIds   = array_column($deck['cards'], 'id');
        $battleId  = Battle::createBattle(Auth::id(), $deckId, $cardIds);

        $this->redirect("/arena/{$battleId}");
    }

    public function join(string $id): void
    {
        $this->requireAuth();
        $deckId = (int)$this->input('deck_id');
        $deck   = Deck::getWithCards($deckId);

        if (!$deck || $deck['user_id'] !== Auth::id()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Deck invalide.'];
            $this->redirect('/arena');
        }

        $cardIds = array_column($deck['cards'], 'id');
        $joined  = Battle::joinBattle((int)$id, Auth::id(), $deckId, $cardIds);

        if (!$joined) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Impossible de rejoindre ce combat.'];
            $this->redirect('/arena');
        }

        $this->redirect("/arena/{$id}");
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $battle = Battle::find((int)$id);

        if (!$battle || ($battle['player1_id'] !== Auth::id() && $battle['player2_id'] !== Auth::id())) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Combat introuvable.'];
            $this->redirect('/arena');
        }

        $gs        = json_decode($battle['game_state'], true);
        $isPlayer1 = $battle['player1_id'] === Auth::id();
        $myHand    = $isPlayer1 ? $gs['p1Hand'] : $gs['p2Hand'];

        // Charge les détails des cartes en main
        $handCards = [];
        foreach ($myHand as $cardId) {
            $handCards[] = Card::find($cardId);
        }

        $this->render('arena/battle', [
            'title'     => 'Combat #' . $id,
            'battle'    => $battle,
            'gs'        => $gs,
            'isPlayer1' => $isPlayer1,
            'handCards' => $handCards,
            'user'      => Auth::user(),
        ]);
    }

    public function play(string $id): void
    {
        $this->requireAuth();
        $cardId = (int)$this->input('card_id');
        $result = Battle::playCard((int)$id, Auth::id(), $cardId);

        $this->json($result);
    }
}
