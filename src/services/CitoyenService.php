<?php

namespace App\Service;

use App\Repository\CitoyenRepository;

class CitoyenService
{
    private static ?CitoyenService $citoyenService = null;
    // Autres méthodes spécifiques au citoyen...
    private CitoyenRepository $citoyenRepository;
    public function getInstance()
    {
        if (self::$citoyenService === null) {
            self::$citoyenService = new CitoyenService();
        }
        return self::$citoyenService;
    }
    private function __construct()
    {
        $this->citoyenRepository = CitoyenRepository::getInstance();
    }

    public function fincByCNi(int $cni) {
        
    }
    // Autres méthodes spécifiques au citoyen...
}
