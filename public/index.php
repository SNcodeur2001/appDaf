<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Gérer erreur .env
}

Router::resolve();
