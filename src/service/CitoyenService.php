<?php
namespace App\Service;

use App\Entity\Citoyen;
use App\Repository\CitoyenRepository;
use App\Service\LoggerService;

class CitoyenService
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

            $this->logRequest($citoyen ? 'Success' : 'Échec', $nci, (int)$responseTime);

            return $citoyen;
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->logRequest('Échec', $nci, (int)$responseTime);
            throw $e;
        }
    }

    public function createCitoyen(array $data): Citoyen
    {
        try {
            $existing = $this->citoyenRepository->findByNci($data['nci']);
            if ($existing) {
                throw new \InvalidArgumentException("Un citoyen avec ce NCI existe déjà");
            }

            $citoyen = new Citoyen(
                $data['nci'],
                $data['nom'],
                $data['prenom'],
                $data['date_naissance'],
                $data['lieu_naissance'],
                $data['url_photo_identite'] ?? null
            );

            $success = $this->citoyenRepository->save($citoyen);
            if (!$success) {
                throw new \Exception("Erreur lors de la création du citoyen");
            }

            $this->logRequest('Success', $data['nci'], 0, '/api/citoyens', 'POST');

            return $citoyen;
        } catch (\Exception $e) {
            $this->logRequest('Échec', $data['nci'] ?? null, 0, '/api/citoyens', 'POST');
            throw $e;
        }
    }

    public function getAllCitoyens(): array
    {
        return $this->citoyenRepository->findAll();
    }

    private function logRequest(string $statut, ?string $nci, int $responseTime, ?string $endpoint = null, ?string $method = null): void
    {
        $this->loggerService->logRequest(
            $statut,
            $nci,
            null,
            $endpoint ?? '/api/citoyen/' . $nci,
            $method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $responseTime
        );
    }
}
