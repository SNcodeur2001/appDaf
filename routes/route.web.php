<?php

use App\Controller\CompteController;
use App\Controller\SecurityController;
use App\Controller\CitoyenController;
use App\Core\Router;

// Routes API pour AppDAF

// Route pour rechercher un citoyen par NCI
// GET /api/citoyen/nci/{nci}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/\/api\/citoyen\/nci\/([^\/]+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $nci = $matches[1];
    $controller = CitoyenController::getInstance();
    $controller->findByNci($nci);
}

// Route pour rechercher un citoyen par NCI via query parameter
// GET /api/citoyen?nci=XXX
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/api/citoyen') {
    $controller = CitoyenController::getInstance();
    $controller->show();
}

// Route pour lister tous les citoyens
// GET /api/citoyens
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/citoyens') {
    $controller = CitoyenController::getInstance();
    $controller->index();
}

// Route pour créer un nouveau citoyen
// POST /api/citoyens
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/citoyens') {
    $controller = CitoyenController::getInstance();
    $controller->store();
}

// Route de test pour vérifier que l'API fonctionne
// GET /api/health
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/api/health') {
    header('Content-Type: application/json');
    echo json_encode([
        'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
        'statut' => 'success',
        'code' => 200,
        'message' => 'AppDAF API is running'
    ]);
}

// Route par défaut - 404
else {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode([
        'data' => null,
        'statut' => 'error',
        'code' => 404,
        'message' => 'Endpoint non trouvé'
    ]);
}

// Routes publiques (pour les invités uniquement)
// Router::get('/', SecurityController::class, 'index');


