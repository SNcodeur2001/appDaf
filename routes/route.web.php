<?php

use App\Controller\CompteController;
use App\Controller\SecurityController;
use App\Controller\CitoyenController;

// Récupération du chemin sans query string
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// === ROUTES API === //
switch (true) {
    // GET /api/health
    case $method === 'GET' && $path === '/api/health':
        header('Content-Type: application/json');
        echo json_encode([
            'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
            'statut' => 'success',
            'code' => 200,
            'message' => 'AppDAF API is running'
        ]);
        break;

    // GET /api/citoyen?nci=XXX
    case $method === 'GET' && $path === '/api/citoyen' && isset($_GET['nci']):
        CitoyenController::getInstance()->show();
        break;

    // GET /api/citoyen/nci/{nci}
    case $method === 'GET' && preg_match('#^/api/citoyen/nci/([^/]+)$#', $path, $matches):
        $nci = $matches[1];
        CitoyenController::getInstance()->findByNci($nci);
        break;

    // GET /api/citoyens
    case $method === 'GET' && $path === '/api/citoyens':
        CitoyenController::getInstance()->index();
        break;

    // POST /api/citoyens
    case $method === 'POST' && $path === '/api/citoyens':
        CitoyenController::getInstance()->store();
        break;

    // Si aucune route ne correspond
    default:
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 404,
            'message' => 'Endpoint non trouvé'
        ]);
        break;
}
