<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '5433';
    $dbname = $_ENV['DB_NAME'] ?? 'pgdbDaf';
    $username = $_ENV['DB_USER'] ?? 'pguserDaf';
    $password = $_ENV['DB_PASSWORD'] ?? 'pgpassword';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Connexion à la base de données réussie!\n";

    // Vérifier si on doit reset les données
    $reset = in_array('--reset', $argv);

    if ($reset) {
        echo "Suppression des données existantes...\n";
        $pdo->exec("TRUNCATE TABLE request_logs RESTART IDENTITY CASCADE");
        $pdo->exec("TRUNCATE TABLE citoyens RESTART IDENTITY CASCADE");
        echo "Données supprimées.\n";
    }

    // Données de test pour les citoyens
    $citoyens = [
        [
            'nci' => '1987654321012',
            'nom' => 'DIOP',
            'prenom' => 'Aminata',
            'date_naissance' => '1990-03-15',
            'lieu_naissance' => 'Dakar',
            'url_photo_identite' => 'https://example-cloud.com/photos/1987654321012.jpg'
        ],
        [
            'nci' => '1876543210987',
            'nom' => 'FALL',
            'prenom' => 'Moussa',
            'date_naissance' => '1985-07-22',
            'lieu_naissance' => 'Saint-Louis',
            'url_photo_identite' => 'https://example-cloud.com/photos/1876543210987.jpg'
        ],
        [
            'nci' => '1765432109876',
            'nom' => 'NDIAYE',
            'prenom' => 'Fatou',
            'date_naissance' => '1992-11-08',
            'lieu_naissance' => 'Thiès',
            'url_photo_identite' => 'https://example-cloud.com/photos/1765432109876.jpg'
        ],
        [
            'nci' => '1654321098765',
            'nom' => 'SALL',
            'prenom' => 'Ibrahima',
            'date_naissance' => '1988-05-13',
            'lieu_naissance' => 'Kaolack',
            'url_photo_identite' => 'https://example-cloud.com/photos/1654321098765.jpg'
        ],
        [
            'nci' => '1543210987654',
            'nom' => 'BA',
            'prenom' => 'Awa',
            'date_naissance' => '1995-09-27',
            'lieu_naissance' => 'Ziguinchor',
            'url_photo_identite' => 'https://example-cloud.com/photos/1543210987654.jpg'
        ]
    ];

    echo "Insertion des citoyens de test...\n";

    $stmt = $pdo->prepare("
        INSERT INTO citoyens (nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite) 
        VALUES (:nci, :nom, :prenom, :date_naissance, :lieu_naissance, :url_photo_identite)
    ");

    foreach ($citoyens as $citoyen) {
        $stmt->execute($citoyen);
        echo "Citoyen ajouté: {$citoyen['prenom']} {$citoyen['nom']} (NCI: {$citoyen['nci']})\n";
    }

    echo "\nDonnées de test insérées avec succès!\n";
    echo "\n=== Citoyens de test disponibles ===\n";
    
    foreach ($citoyens as $citoyen) {
        echo "NCI: {$citoyen['nci']} - {$citoyen['prenom']} {$citoyen['nom']}\n";
    }
    
    echo "\n=== Exemples de requêtes API ===\n";
    echo "GET /api/citoyen/nci/1987654321012\n";
    echo "GET /api/citoyen?nci=1876543210987\n";
    echo "GET /api/citoyens\n";
    echo "GET /api/health\n";

} catch (PDOException $e) {
    echo "Erreur de base de données: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
