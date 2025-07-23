<?php

use App\Core\Router;

// ✅ Health check
Router::get('/api/health', '', '', [], function () {
    header('Content-Type: application/json');
    echo json_encode([
        'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
        'statut' => 'success',
        'code' => 200,
        'message' => 'AppDAF API is running'
    ]);
});

// ✅ Recherche citoyen via paramètre dynamique
Router::get('/api/citoyen/nci/{nci}', 'App\\Controller\\CitoyenController', 'findByNci');

// ✅ Recherche citoyen via query param
Router::get('/api/citoyen', 'App\\Controller\\CitoyenController', 'show');

// ✅ Liste complète des citoyens
Router::get('/api/citoyens', 'App\\Controller\\CitoyenController', 'index');

// ✅ Création d’un citoyen
Router::post('/api/citoyens', 'App\\Controller\\CitoyenController', 'store');

// ✅ Méthodes non autorisées simulées
Router::post('/api/citoyen/edit', 'App\\Controller\\CitoyenController', 'edit');
Router::post('/api/citoyen/delete', 'App\\Controller\\CitoyenController', 'destroy');
Router::post('/api/citoyen/create', 'App\\Controller\\CitoyenController', 'create');
