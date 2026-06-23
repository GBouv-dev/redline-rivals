<?php

// Auth
$router->get('/login',    'AuthController@loginForm');
$router->post('/login',   'AuthController@login');
$router->get('/logout',   'AuthController@logout');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register','AuthController@register');

// Accueil
$router->get('/', 'HomeController@index');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Profil
$router->get('/profil',              'ProfileController@index');
$router->post('/profil/claim/{key}', 'ProfileController@claim');

// Admin
$router->get('/admin', 'AdminController@index');

// Admin — gestion des cartes
$router->get('/admin/cards',             'AdminController@cards');
$router->post('/admin/cards/create',     'AdminController@createCard');
$router->get('/admin/cards/{id}/edit',   'AdminController@editCard');
$router->post('/admin/cards/{id}/update', 'AdminController@updateCard');
$router->post('/admin/cards/{id}/delete', 'AdminController@deleteCard');

// Classement
$router->get('/classement', 'LeaderboardController@index');

// Cartes
$router->get('/cards',            'CardController@index');
$router->get('/cards/collection', 'CardController@collection');
$router->get('/cards/{id}',       'CardController@show');
$router->post('/cards/{id}/finish', 'CardController@setFinish');

// Boosters
$router->get('/boosters',       'BoosterController@index');
$router->post('/boosters/buy',  'BoosterController@buy');
$router->post('/boosters/open', 'BoosterController@open');

// Decks
$router->get('/decks',              'DeckController@index');
$router->post('/decks/create',      'DeckController@create');
$router->get('/decks/{id}',         'DeckController@show');
$router->post('/decks/{id}/add',    'DeckController@addCard');
$router->post('/decks/{id}/remove', 'DeckController@removeCard');
$router->post('/decks/{id}/delete', 'DeckController@delete');

// Arena (combat)
$router->get('/arena',              'BattleController@index');
$router->post('/arena/create',      'BattleController@create');
$router->post('/arena/{id}/join',   'BattleController@join');
$router->get('/arena/{id}',         'BattleController@show');
$router->post('/arena/{id}/play',   'BattleController@play');

// Marché
$router->get('/market',          'MarketController@index');
$router->post('/market/list',    'MarketController@listCard');
$router->post('/market/buy',     'MarketController@buy');
$router->post('/market/cancel',  'MarketController@cancel');

// Enchères
$router->get('/auctions',         'AuctionController@index');
$router->post('/auctions/list',   'AuctionController@listCard');
$router->post('/auctions/bid',    'AuctionController@bid');
