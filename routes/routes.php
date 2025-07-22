<?php
use App\Core\Router;

Router::get('/api/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
        'statut' => 'success',
        'code' => 200,
        'message' => 'AppDAF API is running'
    ]);
});

Router::get('/api/citoyen/nci/{nci}', 'App\Controller\CitoyenController', 'findByNci');
Router::get('/api/citoyen', 'App\Controller\CitoyenController', 'show');
Router::get('/api/citoyens', 'App\Controller\CitoyenController', 'index');
Router::post('/api/citoyens', 'App\Controller\CitoyenController', 'store');
