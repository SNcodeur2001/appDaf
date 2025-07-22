<?php

namespace App\Core\Abstract;

use PDO;
use PDOException;

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    protected function getConnection(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '5433';
        $dbname = $_ENV['DB_NAME'] ?? 'pgdbDaf';
        $username = $_ENV['DB_USER'] ?? 'pguserDaf';
        $password = $_ENV['DB_PASSWORD'] ?? 'pgpassword';

        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
}
