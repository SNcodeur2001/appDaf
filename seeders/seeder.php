<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;

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
        $pdo->exec("TRUNCATE TABLE citoyen RESTART IDENTITY CASCADE");
        echo "Données supprimées.\n";
    }

    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
            'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
            'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
        ],
        'url' => [
            'secure' => true
        ]
    ]);

    // Données de test pour les citoyens (ajoute le nom du fichier image réel dans 'image')
    $citoyen = [
        [
            'nci' => '1987654321012',
            'nom' => 'DIOP',
            'prenom' => 'Aminata',
            'date_naissance' => '1990-03-15',
            'lieu_naissance' => 'Dakar',
            'image' => 'Image collée.png'
        ],
        [
            'nci' => '1876543210987',
            'nom' => 'FALL',
            'prenom' => 'Moussa',
            'date_naissance' => '1985-07-22',
            'lieu_naissance' => 'Saint-Louis',
            'image' => 'Mariama Baldé.jpg'
        ],
        [
            'nci' => '1765432109876',
            'nom' => 'NDIAYE',
            'prenom' => 'Fatou',
            'date_naissance' => '1992-11-08',
            'lieu_naissance' => 'Thiès',
            'image' => 'IMG_7946.JPG'
        ],
        [
            'nci' => '1654321098765',
            'nom' => 'SALL',
            'prenom' => 'Ibrahima',
            'date_naissance' => '1988-05-13',
            'lieu_naissance' => 'Kaolack',
            'image' => 'WhatsApp Image 2025-04-06 at 6.16.42 PM.jpeg'
        ],
        [
            'nci' => '1543210987654',
            'nom' => 'BA',
            'prenom' => 'Awa',
            'date_naissance' => '1995-09-27',
            'lieu_naissance' => 'Ziguinchor',
            'image' => 'IMG_7946.JPG'
        ],
        [
            'nci' => '9876543210123',
            'nom' => 'DIALLO',
            'prenom' => 'Mouhamadou',
            'date_naissance' => '1988-12-01',
            'lieu_naissance' => 'Tambacounda',
            'image' => 'WhatsApp Image 2025-04-06 at 6.16.42 PM.jpeg'
        ]
    ];

    echo "Insertion des citoyens de test...\n";

    $stmt = $pdo->prepare("
        INSERT INTO citoyen (nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite) 
        VALUES (:nci, :nom, :prenom, :date_naissance, :lieu_naissance, :url_photo_identite)
    ");

    foreach ($citoyen as $c) {
        $localPath = __DIR__ . '/images/' . $c['image'];
        if (!file_exists($localPath)) {
            echo "Image non trouvée pour {$c['prenom']} {$c['nom']} : $localPath\n";
            continue;
        }

        // Upload sur Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload($localPath, [
            'folder' => 'appdaf/cni',
            'public_id' => pathinfo($c['image'], PATHINFO_FILENAME),
            'overwrite' => true,
            'resource_type' => 'image'
        ]);
        $c['url_photo_identite'] = $uploadResult['secure_url'];

        // Insertion en base
        $stmt->execute([
            'nci' => $c['nci'],
            'nom' => $c['nom'],
            'prenom' => $c['prenom'],
            'date_naissance' => $c['date_naissance'],
            'lieu_naissance' => $c['lieu_naissance'],
            'url_photo_identite' => $c['url_photo_identite'],
        ]);
        echo "✔️ {$c['prenom']} {$c['nom']} ajouté avec image Cloudinary\n";
    }

    echo "\nDonnées de test insérées avec succès!\n";
    echo "\n=== Citoyens de test disponibles ===\n";
    
    foreach ($citoyen as $c) {
        echo "NCI: {$c['nci']} - {$c['prenom']} {$c['nom']}\n";
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
