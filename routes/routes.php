<?php

use App\Core\Router;

// ✅ Health check
Router::get('/api/health', '', '', [], function() {
    header('Content-Type: application/json');
    echo json_encode([
        'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
        'statut' => 'success',
        'code' => 200,
        'message' => 'AppDAF API is running'
    ]);
});

// ✅ Recherche citoyen via NCI
Router::get('/api/citoyen/nci/{nci}', 'App\\Controller\\CitoyenController', 'findByNci');

// ✅ Par query param
Router::get('/api/citoyen', 'App\\Controller\\CitoyenController', 'show');

// ✅ Liste complète
Router::get('/api/citoyens', 'App\\Controller\\CitoyenController', 'index');

// ✅ Création
Router::post('/api/citoyens', 'App\\Controller\\CitoyenController', 'store');
