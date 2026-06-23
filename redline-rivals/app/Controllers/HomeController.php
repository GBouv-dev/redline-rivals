<?php

require_once ROOT . '/app/Models/Card.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $cards = Card::all();
        $this->render('home/index', [
            'title' => 'Redline Rivals',
            'cards' => $cards,
            'user'  => Auth::check() ? Auth::user() : null,
        ]);
    }
}