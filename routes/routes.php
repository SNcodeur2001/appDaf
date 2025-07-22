<?php

// Configuration des routes AppDAF
return [
    // API Health Check
    [
        'method' => 'GET',
        'path' => '/api/health',
        'action' => function() {
            header('Content-Type: application/json');
            echo json_encode([
                'data' => ['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')],
                'statut' => 'success',
                'code' => 200,
                'message' => 'AppDAF API is running'
            ]);
        }
    ],
    
    // Recherche citoyen par NCI (URL parameter)
    [
        'method' => 'GET',
        'path' => '/api/citoyen/nci/{nci}',
        'controller' => 'CitoyenController',
        'action' => 'findByNci'
    ],
    
    // Recherche citoyen par NCI (Query parameter)
    [
        'method' => 'GET',
        'path' => '/api/citoyen',
        'controller' => 'CitoyenController',
        'action' => 'show'
    ],
    
    // Liste des citoyens
    [
        'method' => 'GET',
        'path' => '/api/citoyens',
        'controller' => 'CitoyenController',
        'action' => 'index'
    ],
    
    // Création d'un citoyen
    [
        'method' => 'POST',
        'path' => '/api/citoyens',
        'controller' => 'CitoyenController',
        'action' => 'store'
    ]
];
