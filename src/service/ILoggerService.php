<?php

namespace App\Service;

interface ILoggerService
{
    public function logRequest(
        string $statut,
        ?string $nciRecherche = null,
        ?string $localisation = null,
        ?string $endpoint = null,
        ?string $method = null,
        ?int $responseTime = null
    ): void;

    public function getRequestLogs(int $limit = 100, int $offset = 0): array;
    public function getClientIpAddress(): string;
}
