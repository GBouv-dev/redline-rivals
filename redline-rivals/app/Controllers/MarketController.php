<?php

require_once ROOT . '/app/Models/MarketListing.php';
require_once ROOT . '/app/Models/UserCard.php';
require_once ROOT . '/app/Models/Card.php';

class MarketController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $this->render('market/index', [
            'title'      => 'Marché',
            'user'       => Auth::user(),
            'listings'   => MarketListing::getActive(Auth::id()),
            'myListings' => MarketListing::getBySeller(Auth::id()),
            'collection' => UserCard::getCollection(Auth::id()),
        ]);
    }

    public function listCard(): void
    {
        $this->requireAuth();
        $cardId   = (int)$this->input('card_id');
        $price    = (int)$this->input('price');
        $quantity = max(1, (int)$this->input('quantity', 1));

        if ($price < 1) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Prix invalide.'];
            $this->redirect('/market');
        }

        $ok = MarketListing::list(Auth::id(), $cardId, $price, $quantity);

        $_SESSION['flash'] = $ok
            ? ['type' => 'success', 'message' => 'Carte mise en vente !']
            : ['type' => 'error',   'message' => 'Vous ne possédez pas assez de cette carte.'];

        $this->redirect('/market');
    }

    public function buy(): void
    {
        $this->requireAuth();
        $listingId = (int)$this->input('listing_id');
        $result    = MarketListing::buy($listingId, Auth::id());

        // Rafraîchit la session avec les nouveaux coins
        $updatedUser = Database::queryOne("SELECT * FROM users WHERE id = ?", [Auth::id()]);
        Auth::login($updatedUser);

        $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
        $this->redirect('/market');
    }

    public function cancel(): void
    {
        $this->requireAuth();
        $listingId = (int)$this->input('listing_id');
        $ok        = MarketListing::cancel($listingId, Auth::id());

        $_SESSION['flash'] = $ok
            ? ['type' => 'success', 'message' => 'Annonce annulée, carte restituée.']
            : ['type' => 'error',   'message' => 'Impossible d\'annuler cette annonce.'];

        $this->redirect('/market');
    }
}
