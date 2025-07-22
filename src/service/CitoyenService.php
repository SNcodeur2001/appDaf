<?php

namespace App\Service;

use App\Entity\Citoyen;
use App\Repository\CitoyenRepository;

class CitoyenService
{
    private CitoyenRepository $citoyenRepository;
    private LoggerService $loggerService;
    private static ?CitoyenService $instance = null;

    private function __construct()
    {
        $this->citoyenRepository = CitoyenRepository::getInstance();
        $this->loggerService = LoggerService::getInstance();
    }

    public static function getInstance(): CitoyenService
    {
        if (self::$instance === null) {
            self::$instance = new CitoyenService();
        }
        return self::$instance;
    }

    public function findCitoyenByNci(string $nci): ?Citoyen
    {
        $startTime = microtime(true);
        
        try {
            if (empty(trim($nci))) {
                throw new \InvalidArgumentException("Le NCI ne peut pas être vide");
            }

            $citoyen = $this->citoyenRepository->findByNci($nci);
            
            $responseTime = (microtime(true) - $startTime) * 1000; // en millisecondes
            
            if ($citoyen) {
                $this->loggerService->logRequest(
                    'Success',
                    $nci,
                    null,
                    '/api/citoyen/' . $nci,
                    $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    (int)$responseTime
                );
                return $citoyen;
            } else {
                $this->loggerService->logRequest(
                    'Échec',
                    $nci,
                    null,
                    '/api/citoyen/' . $nci,
                    $_SERVER['REQUEST_METHOD'] ?? 'GET',
                    (int)$responseTime
                );
                return null;
            }
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->loggerService->logRequest(
                'Échec',
                $nci,
                null,
                '/api/citoyen/' . $nci,
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                (int)$responseTime
            );
            throw $e;
        }
    }

    public function createCitoyen(array $data): Citoyen
    {
        try {
            // Validation des données requises
            $requiredFields = ['nci', 'nom', 'prenom', 'date_naissance', 'lieu_naissance'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \InvalidArgumentException("Le champ '$field' est requis");
                }
            }

            // Vérifier si le NCI existe déjà
            $existingCitoyen = $this->citoyenRepository->findByNci($data['nci']);
            if ($existingCitoyen) {
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

            $this->loggerService->logRequest(
                'Success',
                $data['nci'],
                null,
                '/api/citoyen',
                'POST'
            );

            return $citoyen;
        } catch (\Exception $e) {
            $this->loggerService->logRequest(
                'Échec',
                $data['nci'] ?? null,
                null,
                '/api/citoyen',
                'POST'
            );
            throw $e;
        }
    }

    public function getAllCitoyens(): array
    {
        try {
            return $this->citoyenRepository->findAll();
        } catch (\Exception $e) {
            $this->loggerService->logRequest(
                'Échec',
                null,
                null,
                '/api/citoyens',
                'GET'
            );
            throw $e;
        }
    }
}
