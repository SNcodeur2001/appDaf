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

// 🏠 Citoyen - Lecture
Router::get('/api/citoyens', 'App\\Controller\\CitoyenController', 'index');
Router::get('/api/citoyen', 'App\\Controller\\CitoyenController', 'show');
Router::get('/api/citoyen/nci/{nci}', 'App\\Controller\\CitoyenController', 'findByNci');

// 🛠️ Citoyen - Écriture
Router::post('/api/citoyens', 'App\\Controller\\CitoyenController', 'store');
Router::post('/api/citoyen/create', 'App\\Controller\\CitoyenController', 'create');
Router::post('/api/citoyen/edit', 'App\\Controller\\CitoyenController', 'edit');
Router::post('/api/citoyen/delete', 'App\\Controller\\CitoyenController', 'destroy');


