<?php
// Vérifier si autoload.php existe avant de l'inclure
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("❌ Error: Composer autoload file not found at: $autoloadPath\n" .
        "Please run 'composer install' to install dependencies.\n");
}

require_once $autoloadPath;

// Charger les variables d'environnement
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Si le fichier .env n'existe pas, utiliser les valeurs par défaut
}

// Définir les headers CORS pour l'API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Lancer le système de routage
    \App\Core\Router::resolve();
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'data' => null,
        'statut' => 'error',
        'code' => 500,
        'message' => 'Erreur interne du serveur: ' . $e->getMessage()
    ]);
}