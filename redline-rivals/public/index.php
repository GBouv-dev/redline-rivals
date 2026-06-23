<?php

define('ROOT', dirname(__DIR__));

require_once ROOT . '/config/config.php';
require_once ROOT . '/app/Core/Database.php';
require_once ROOT . '/app/Core/Model.php';
require_once ROOT . '/app/Core/Controller.php';
require_once ROOT . '/app/Core/Auth.php';
require_once ROOT . '/app/Core/Router.php';

session_start();

$router = new Router();
require_once ROOT . '/config/routes.php';
$router->dispatch();
