<?php
namespace App\Repository;
use App\Core\abstract\AbstractRepository;
use Pdo;


   class CitoyenRepository extends AbstractRepository
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
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

            $stmt->execute([
                ':localisation'   => $localisation,
                ':ip_address'     => $ipAddress,
                ':statut'         => $statut,
                ':nci_recherche'  => $nciRecherche,
                ':endpoint'       => $endpoint,
                ':method'         => $method,
                ':user_agent'     => $userAgent,
                ':response_time'  => $responseTime,
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la journalisation: " . $e->getMessage());
        }
    }

    private function getClientIpAddress(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
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
            
            $stmt->execute([
                ':limit' => $limit,
                ':offset' => $offset
            ]);

            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des logs: " . $e->getMessage());
        }
    }
}
