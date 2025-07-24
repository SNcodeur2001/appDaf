<?php
// Tableau de routes sans le préfixe /api
return [
    // Route racine
    [
        'method' => 'GET',
        'path' => '/',
        'action' => function() {
            header('Content-Type: application/json');
            echo json_encode([
                'data' => [
                    'app' => 'AppDAF API',
                    'version' => '1.0.0',
                    'endpoints' => [
                        'GET /health' => 'Vérification de l\'état de l\'API',
                        'GET /citoyens' => 'Liste des citoyens',
                        'GET /citoyen?nci={nci}' => 'Recherche citoyen par NCI',
                        'POST /citoyens' => 'Créer un citoyen'
                    ]
                ],
                'statut' => 'success',
                'code' => 200,
                'message' => 'Bienvenue sur l\'API AppDAF'
            ]);
        }
    ],
    // Health Check
    [
        'method' => 'GET',
        'path' => '/health',
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
    // Recherche citoyen par NCI (URL param)
    [
        'method' => 'GET',
        'path' => '/citoyen/nci/{nci}',
        'controller' => 'CitoyenController',
        'action' => 'findByNci'
    ],
    // Recherche citoyen par NCI (Query param)
    [
        'method' => 'GET',
        'path' => '/citoyen',
        'controller' => 'CitoyenController',
        'action' => 'show'
    ],
    // Liste des citoyens
    [
        'method' => 'GET',
        'path' => '/citoyens',
        'controller' => 'CitoyenController',
        'action' => 'index'
    ],
    // Création d'un citoyen
    [
        'method' => 'POST',
        'path' => '/citoyens',
        'controller' => 'CitoyenController',
        'action' => 'store'
    ]
];




