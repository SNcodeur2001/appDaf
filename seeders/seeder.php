<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $host = $_ENV['DB_HOST'] ?? 'turntable.proxy.rlwy.net';
    $port = $_ENV['DB_PORT'] ?? '34111';
    $dbname = $_ENV['DB_NAME'] ?? 'railway';
    $username = $_ENV['DB_USER'] ?? 'postgres';
    $password = $_ENV['DB_PASSWORD'] ?? 'CJLhOPlzHWxccQakefskJUZiaUyAxERX';
    $databaseurl = $_ENV['DATABASE_URL'] ?? 'postgresql://postgres:CJLhOPlzHWxccQakefskJUZiaUyAxERX@turntable.proxy.rlwy.net:34111/railway';

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
    $citoyens = [
        [
            'nci' => '1987654321012',
            'nom' => 'DIOP',
            'prenom' => 'Aminata',
            'date_naissance' => '1990-03-15',
            'lieu_naissance' => 'Dakar',
            'image' => 'IMG_7946.JPG'
        ],
        [
            'nci' => '1876543210987',
            'nom' => 'FALL',
            'prenom' => 'Moussa',
            'date_naissance' => '1985-07-22',
            'lieu_naissance' => 'Saint-Louis',
            'image' => 'WhatsApp Image 2025-04-06 at 6.16.42 PM.jpeg'
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
        INSERT INTO citoyens (nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite) 
        VALUES (:nci, :nom, :prenom, :date_naissance, :lieu_naissance, :url_photo_identite)
    ");

    foreach ($citoyens as $citoyen) {
        $localPath = __DIR__ . '/images/' . $citoyen['image'];
        if (!file_exists($localPath)) {
            echo "Image non trouvée pour {$citoyen['prenom']} {$citoyen['nom']} : $localPath\n";
            continue;
        }

        // Upload sur Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload($localPath, [
            'folder' => 'appdaf/cni',
            'public_id' => pathinfo($citoyen['image'], PATHINFO_FILENAME),
            'overwrite' => true,
            'resource_type' => 'image'
        ]);
        $citoyen['url_photo_identite'] = $uploadResult['secure_url'];

        // Insertion en base
        $stmt->execute([
            'nci' => $citoyen['nci'],
            'nom' => $citoyen['nom'],
            'prenom' => $citoyen['prenom'],
            'date_naissance' => $citoyen['date_naissance'],
            'lieu_naissance' => $citoyen['lieu_naissance'],
            'url_photo_identite' => $citoyen['url_photo_identite'],
        ]);
        echo "✔️ {$citoyen['prenom']} {$citoyen['nom']} ajouté avec image Cloudinary\n";
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
