<?php

namespace App\Repository;

use App\Core\Abstract\AbstractRepository;
use PDO;
use PDOException;

class LoggerRepository extends AbstractRepository
{
    public function insertLog(array $data): void
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO request_logs 
                (date_heure, localisation, ip_address, statut, nci_recherche, endpoint, method, user_agent, response_time_ms) 
                VALUES (CURRENT_TIMESTAMP, :localisation, :ip_address, :statut, :nci_recherche, :endpoint, :method, :user_agent, :response_time)
            ");
            $stmt->bindParam(':localisation', $data['localisation'], PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $data['ip_address'], PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
            $stmt->bindParam(':nci_recherche', $data['nci_recherche'], PDO::PARAM_STR);
            $stmt->bindParam(':endpoint', $data['endpoint'], PDO::PARAM_STR);
            $stmt->bindParam(':method', $data['method'], PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $data['user_agent'], PDO::PARAM_STR);
            $stmt->bindParam(':response_time', $data['response_time'], PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la journalisation: " . $e->getMessage());
        }
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
