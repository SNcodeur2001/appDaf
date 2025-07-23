<?php

namespace App\Repository;
use App\Entity\Journal;

interface ILoggerRepository
{
    public function insertLog(array $data): void;
    public function getRequestLogs(int $limit = 100, int $offset = 0): array;
}