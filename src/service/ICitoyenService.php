<?php


namespace App\Service;
use App\Entity\Citoyen;

interface ICitoyenService
{
    public function createCitoyen(array $data): Citoyen;
    public function getAllCitoyens(): array;
    public function findCitoyenByNci(string $nci): ?Citoyen;
    public function logRequest(
        string $statut,
        ?string $nciRecherche = null,
        ?int $responseTime = null,
        ?string $endpoint = null,
        ?string $method = null
    ): void;
}
