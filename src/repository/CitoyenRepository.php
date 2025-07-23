<?php

namespace App\Repository;

use App\Entity\Citoyen;
use PDO;

class CitoyenRepository
{
    private PDO $pdo;

    public function __construct()
    {
        // Connexion à la base PostgreSQL via PDO
        $dsn = DSN;
        $user = DB_USER;
        $password = DB_PASSWORD;
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM citoyens');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($item) {
            return new Citoyen(
                $item['nci'],
                $item['nom'],
                $item['prenom'],
                $item['date_naissance'],
                $item['lieu_naissance'],
                $item['url_photo_identite'] ?? null,
                $item['id'] ?? null
            );
        }, $rows);
    }

    public function findByNci(string $nci): ?Citoyen
    {
        $stmt = $this->pdo->prepare('SELECT * FROM citoyens WHERE nci = :nci LIMIT 1');
        $stmt->execute(['nci' => $nci]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($item) {
            return new Citoyen(
                $item['nci'],
                $item['nom'],
                $item['prenom'],
                $item['date_naissance'],
                $item['lieu_naissance'],
                $item['url_photo_identite'] ?? null,
                $item['id'] ?? null
            );
        }
        return null;
    }

    public function save(Citoyen $citoyen): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO citoyens (nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite) VALUES (:nci, :nom, :prenom, :date_naissance, :lieu_naissance, :url_photo_identite)');
        return $stmt->execute([
            'nci' => $citoyen->getNci(),
            'nom' => $citoyen->getNom(),
            'prenom' => $citoyen->getPrenom(),
            'date_naissance' => $citoyen->getDateNaissance(),
            'lieu_naissance' => $citoyen->getLieuNaissance(),
            'url_photo_identite' => $citoyen->getUrlPhotoIdentite()
        ]);
    }
}
