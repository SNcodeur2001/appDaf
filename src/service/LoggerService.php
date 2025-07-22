<?php

namespace App\Service;

use PDO;
use PDOException;

class LoggerService
{
    private PDO $pdo;
    private static ?LoggerService $instance = null;

    private function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    public static function getInstance(): LoggerService
    {
        if (self::$instance === null) {
            self::$instance = new LoggerService();
        }
        return self::$instance;
    }

    private function getConnection(): PDO
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
            throw new \Exception("Erreur de connexion à la base de données pour le logger: " . $e->getMessage());
        }
    }

    public function logRequest(
        string $statut,
        ?string $nciRecherche = null,
        ?string $localisation = null,
        ?string $endpoint = null,
        ?string $method = null,
        ?int $responseTime = null
    ): void {
        try {
            $ipAddress = $this->getClientIpAddress();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $stmt = $this->pdo->prepare("
                INSERT INTO request_logs 
                (date_heure, localisation, ip_address, statut, nci_recherche, endpoint, method, user_agent, response_time_ms) 
                VALUES (CURRENT_TIMESTAMP, :localisation, :ip_address, :statut, :nci_recherche, :endpoint, :method, :user_agent, :response_time)
            ");

            $stmt->bindParam(':localisation', $localisation, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
            $stmt->bindParam(':nci_recherche', $nciRecherche, PDO::PARAM_STR);
            $stmt->bindParam(':endpoint', $endpoint, PDO::PARAM_STR);
            $stmt->bindParam(':method', $method, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
            $stmt->bindParam(':response_time', $responseTime, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            // En cas d'erreur de log, on ne doit pas faire échouer la requête principale
            error_log("Erreur lors de la journalisation: " . $e->getMessage());
        }
    }

    private function getClientIpAddress(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Pour X-Forwarded-For, prendre la première IP
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Valider que c'est une IP valide
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function getRequestLogs(int $limit = 100, int $offset = 0): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM request_logs 
                ORDER BY date_heure DESC 
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des logs: " . $e->getMessage());
        }
    }
}
