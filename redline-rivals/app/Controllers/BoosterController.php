<?php

require_once ROOT . '/app/Models/Booster.php';
require_once ROOT . '/app/Models/UserBooster.php';
require_once ROOT . '/app/Models/UserCard.php';
require_once ROOT . '/app/Models/User.php';
require_once ROOT . '/app/Models/Card.php';

class BoosterController extends Controller
{
    // Liste des boosters disponibles à l'achat
    public function index(): void
    {
        $this->requireAuth();
        $boosters  = Booster::allActive();
        $inventory = UserBooster::getInventory(Auth::id());

        $this->render('boosters/index', [
            'title'     => 'Boutique Boosters',
            'boosters'  => $boosters,
            'inventory' => $inventory,
            'user'      => Auth::user(),
        ]);
    }

    // Achète un booster
    public function buy(): void
    {
        $this->requireAuth();
        $boosterId = (int) $this->input('booster_id');
        $booster   = Booster::find($boosterId);
        $user      = Auth::user();

        if (!$booster || !$booster['is_active']) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Booster introuvable.'];
            $this->redirect('/boosters');
        }

        if ($user['coins'] < $booster['price']) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Pas assez de coins !'];
            $this->redirect('/boosters');
        }

        // Débite les coins
        User::update(Auth::id(), ['coins' => $user['coins'] - $booster['price']]);

        // Ajoute le booster à l'inventaire
        UserBooster::addBooster(Auth::id(), $boosterId);

        // Rafraîchit la session
        $updatedUser = User::find(Auth::id());
        Auth::login($updatedUser);

        $_SESSION['flash'] = ['type' => 'success', 'message' => "Booster \"{$booster['name']}\" acheté !"];
        $this->redirect('/boosters');
    }

    // Ouvre un booster et affiche les cartes tirées
    public function open(): void
    {
        $this->requireAuth();
        $boosterId = (int) $this->input('booster_id');

        // Vérifie que le joueur possède ce booster
        $consumed = UserBooster::consume(Auth::id(), $boosterId);
        if (!$consumed) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Vous ne possédez pas ce booster.'];
            $this->redirect('/boosters');
        }

        // Tire les cartes
        $drawnCards = Booster::open($boosterId);

        // Tire une finition au sort par carte et l'ajoute à la collection
        foreach ($drawnCards as $i => $card) {
            $finish = UserCard::rollFinish();
            UserCard::addCard(Auth::id(), $card['id'], $finish);
            $drawnCards[$i]['finish'] = $finish;
        }

        $this->render('boosters/open', [
            'title'      => 'Ouverture du booster',
            'drawnCards' => $drawnCards,
            'user'       => Auth::user(),
        ]);
    }
}