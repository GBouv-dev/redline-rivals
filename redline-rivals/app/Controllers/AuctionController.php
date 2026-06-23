<?php

require_once ROOT . '/app/Models/Auction.php';
require_once ROOT . '/app/Models/UserCard.php';
require_once ROOT . '/app/Models/Card.php';

class AuctionController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // Clôture les enchères expirées à chaque visite
        Auction::closeExpired();

        $this->render('auction/index', [
            'title'      => 'Enchères',
            'user'       => Auth::user(),
            'auctions'   => Auction::getActive(Auth::id()),
            'collection' => UserCard::getCollection(Auth::id()),
        ]);
    }

    public function listCard(): void
    {
        $this->requireAuth();
        $cardId       = (int)$this->input('card_id');
        $startPrice   = (int)$this->input('start_price');
        $durationHours= max(1, min(72, (int)$this->input('duration', 24)));

        if ($startPrice < 1) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Prix de départ invalide.'];
            $this->redirect('/auctions');
        }

        $ok = Auction::listCard(Auth::id(), $cardId, $startPrice, $durationHours);

        $_SESSION['flash'] = $ok
            ? ['type' => 'success', 'message' => "Carte mise en enchère pour {$durationHours}h !"]
            : ['type' => 'error',   'message' => 'Vous ne possédez pas cette carte.'];

        $this->redirect('/auctions');
    }

    public function bid(): void
    {
        $this->requireAuth();
        $auctionId = (int)$this->input('auction_id');
        $amount    = (int)$this->input('amount');
        $result    = Auction::placeBid($auctionId, Auth::id(), $amount);

        $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'error', 'message' => $result['message']];
        $this->redirect('/auctions');
    }
}
