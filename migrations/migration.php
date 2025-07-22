<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $dsn = "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}";
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie à la base de données\n";
} catch (PDOException $e) {
    die("❌ Connexion échouée : " . $e->getMessage());
}

try {
    $pdo->beginTransaction();

    // 1. citoyens
    $citoyen = [
        [3, 'Sow', 'Mariama', '2567898765432', '2000-06-22', 'Rufisque', 'recto', 'verso'],
        [4, 'Diop', 'Anta', '2987654321123', '2000-02-22', 'Dakar', 'recto', 'verso'],
    ];

    $stmCitoyen = $pdo->prepare("INSERT INTO citoyen (id, nom, prenom, cni, date_Naissance, lieu_Naissance, photo_cni_recto, photo_cni_verso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($citoyen as $row) {
        $stmCitoyen->execute($row);
    }
    echo "Citoyens insérés\n";

    // 2. journal
    $journal = [
        [5, '2025-07-22', 14, '192.168.0.1', 'Dakar', 'SUCCES', '200', '2567898765432'],
    ];

    $stmtJournal = $pdo->prepare("INSERT INTO journal (id, date, heure, ip_adresse, localisation, status, code_http, cni) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($journal as $row) {
        $stmtJournal->execute($row);
    }
    echo "Journal inséré\n";

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de l'insertion des données : " . $e->getMessage());
}
