<?php

namespace App\Service;

use App\Core\Abstract\Singleton;
use App\Entity\Citoyen;
use App\Service\ICitoyenService;
use App\Service\ILoggerService;
use App\Repository\ICitoyenRepository;
class CitoyenService extends Singleton implements ICitoyenService
{
    private ICitoyenRepository $citoyenRepository;
    private ILoggerService $loggerService;

    public function __construct(ICitoyenRepository $citoyenRepository, ILoggerService $loggerService)
    {
        $this->citoyenRepository = $citoyenRepository;
        $this->loggerService = $loggerService;
    }

    public function findCitoyenByNci(string $nci): ?Citoyen
    {
        $startTime = microtime(true);
        
        try {
            $citoyen = $this->citoyenRepository->findByNci($nci);
            
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            
            if ($citoyen) {
                $this->logRequest('Success', $nci, $responseTime);
                return $citoyen;
            } else {
                $this->logRequest('Échec', $nci, $responseTime);
                return null;
            }
        } catch (\Exception $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            $this->logRequest('Échec', $nci, $responseTime);
            throw $e;
        }
    }

    public function createCitoyen(array $data): Citoyen
    {
        $startTime = microtime(true);
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
        $responseTime = (int)((microtime(true) - $startTime) * 1000);
        if (!$success) {
            $this->logRequest('Échec', $data['nci'] ?? null, $responseTime, '/api/citoyen', 'POST');
            throw new \Exception("Erreur lors de la création du citoyen");
        }

        $this->logRequest('Success', $data['nci'], $responseTime, '/api/citoyen', 'POST');
        return $citoyen;
    }

    public function getAllCitoyens(): array
    {
        $startTime = microtime(true);
        try {
            $result = $this->citoyenRepository->findAll();
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            $this->logRequest('Success', null, $responseTime, '/api/citoyens', 'GET');
            return $result;
        } catch (\Exception $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            $this->logRequest('Échec', null, $responseTime, '/api/citoyens', 'GET');
            throw $e;
        }
    }

    public function logRequest(string $statut, ?string $nciRecherche = null, ?int $responseTime = null, ?string $endpoint = null, ?string $method = null): void
    {
        $this->loggerService->logRequest(
            $statut,
            $nciRecherche,
            null,
            $endpoint ?? '/api/citoyen/' . $nciRecherche,
            $method ?? $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $responseTime
        );
    }
}
