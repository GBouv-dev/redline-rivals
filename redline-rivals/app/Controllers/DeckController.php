<?php

require_once ROOT . '/app/Models/Deck.php';
require_once ROOT . '/app/Models/UserCard.php';
require_once ROOT . '/app/Models/Card.php';

class DeckController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->render('decks/index', [
            'title' => 'Mes decks',
            'decks' => Deck::getByUser(Auth::id()),
            'user'  => Auth::user(),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $name = $this->input('name', 'Nouveau deck');

        Deck::create([
            'user_id'    => Auth::id(),
            'name'       => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Deck créé !'];
        $this->redirect('/decks');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $deck = Deck::getWithCards((int)$id);

        if (!$deck || $deck['user_id'] !== Auth::id()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Deck introuvable.'];
            $this->redirect('/decks');
        }

        $collection = UserCard::getCollection(Auth::id());

        $this->render('decks/show', [
            'title'      => $deck['name'],
            'deck'       => $deck,
            'collection' => $collection,
            'user'       => Auth::user(),
        ]);
    }

    public function addCard(string $id): void
    {
        $this->requireAuth();
        $cardId = (int)$this->input('card_id');
        $deck   = Deck::find((int)$id);

        if (!$deck || $deck['user_id'] !== Auth::id()) {
            $this->redirect('/decks');
        }

        if (Deck::cardCount((int)$id) >= 10) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Maximum 10 cartes par deck.'];
            $this->redirect("/decks/{$id}");
        }

        Deck::addCard((int)$id, $cardId);
        $this->redirect("/decks/{$id}");
    }

    public function removeCard(string $id): void
    {
        $this->requireAuth();
        $cardId = (int)$this->input('card_id');
        $deck   = Deck::find((int)$id);

        if (!$deck || $deck['user_id'] !== Auth::id()) {
            $this->redirect('/decks');
        }

        Deck::removeCard((int)$id, $cardId);
        $this->redirect("/decks/{$id}");
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $deck = Deck::find((int)$id);

        if ($deck && $deck['user_id'] === Auth::id()) {
            Deck::delete((int)$id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Deck supprimé.'];
        }

        $this->redirect('/decks');
    }
}
