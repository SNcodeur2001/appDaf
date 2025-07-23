<?php

namespace App\Service;

use App\Core\Abstract\Singleton;
use App\Entity\Citoyen;
use App\Repository\CitoyenRepository;

class CitoyenService extends Singleton
{
    private CitoyenRepository $citoyenRepository;
    private LoggerService $loggerService;

    public function __construct(CitoyenRepository $citoyenRepository, LoggerService $loggerService)
    {
        $this->citoyenRepository = $citoyenRepository;
        $this->loggerService = $loggerService;
    }

    public function findCitoyenByNci(string $nci): ?Citoyen
    {
        $startTime = microtime(true);
        
        try {
            $citoyen = $this->citoyenRepository->findByNci($nci);
            
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            if ($citoyen) {
                $this->logRequest('Success', $nci, $responseTime);
                return $citoyen;
            } else {
                $this->logRequest('Échec', $nci, $responseTime);
                return null;
            }
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->logRequest('Échec', $nci, $responseTime);
            throw $e;
        }
    }

    public function createCitoyen(array $data): Citoyen
    {
        $reflection = new \ReflectionClass(Citoyen::class);
        $citoyen = $reflection->newInstance(
            $data['nci'],
            $data['nom'],
            $data['prenom'],
            $data['date_naissance'],
            $data['lieu_naissance'],
            $data['url_photo_identite'] ?? null
        );

        $success = $this->citoyenRepository->save($citoyen);
        if (!$success) {
            $this->logRequest('Échec', $data['nci'] ?? null, 0, '/api/citoyen', 'POST');
            throw new \Exception("Erreur lors de la création du citoyen");
        }

        $this->logRequest('Success', $data['nci'], 0, '/api/citoyen', 'POST');
        return $citoyen;
    }

    public function getAllCitoyens(): array
    {
        try {
            return $this->citoyenRepository->findAll();
        } catch (\Exception $e) {
            $this->logRequest('Échec', null, 0, '/api/citoyens', 'GET');
            throw $e;
        }
    }

    private function logRequest(string $statut, ?string $nci, int $responseTime, ?string $endpoint = null, ?string $method = null): void
    {
        $this->loggerService->logRequest(
            $statut,
            $nci,
            null,
            $endpoint ?? '/api/citoyen/' . $nci,
            $method ?? $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $responseTime
        );
    }
}
