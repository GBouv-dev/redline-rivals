<?php

// Environnement
define('ENV', 'development'); // 'production' sur PlanetHoster

// Affichage des erreurs
if (ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// URL de base
define('BASE_URL', 'http://redline-rivals.localhost');

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'redline_rivals');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
