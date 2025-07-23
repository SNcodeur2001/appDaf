<?php

namespace App\Service;

use App\Core\Abstract\Singleton;
use App\Repository\LoggerRepository;

class LoggerService extends Singleton
{
    private LoggerRepository $loggerRepository;

    public function __construct(LoggerRepository $loggerRepository)
    {
        $this->loggerRepository = $loggerRepository;
    }

    public function logRequest(
        string $statut,
        ?string $nciRecherche = null,
        ?string $localisation = null,
        ?string $endpoint = null,
        ?string $method = null,
        ?int $responseTime = null
    ): void {
        $ipAddress = $this->getClientIpAddress();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $data = [
            'statut' => $statut,
            'nci_recherche' => $nciRecherche,
            'localisation' => $localisation,
            'endpoint' => $endpoint,
            'method' => $method,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'response_time' => $responseTime,
        ];
        try {
            $this->loggerRepository->insertLog($data);
        } catch (\Exception $e) {
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
        return $this->loggerRepository->getRequestLogs($limit, $offset);
    }
}
