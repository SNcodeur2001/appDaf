<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../app/config/env.php';
use App\Core\Router;

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (Exception $e) {
    // Gérer l'erreur .env ici si nécessaire
}

// 💡 Charger les routes AVANT de résoudre la requête
require_once __DIR__ . '/../routes/route.web.php';

Router::resolve();
