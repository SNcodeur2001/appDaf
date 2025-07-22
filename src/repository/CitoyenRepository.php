<?php

namespace App\Repository;

use App\Entity\Citoyen;

class CitoyenRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/../data/citoyens.json';
    }

    private function readData(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }

        $json = file_get_contents($this->file);
        return json_decode($json, true) ?? [];
    }

    private function writeData(array $data): void
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function findAll(): array
    {
        return array_map(function ($item) {
            return new Citoyen(
                $item['nci'],
                $item['nom'],
                $item['prenom'],
                $item['date_naissance'],
                $item['lieu_naissance'],
                $item['url_photo_identite'] ?? null
            );
        }, $this->readData());
    }

    public function findByNci(string $nci): ?Citoyen
    {
        foreach ($this->readData() as $citoyen) {
            if ($citoyen['nci'] === $nci) {
                return new Citoyen(
                    $citoyen['nci'],
                    $citoyen['nom'],
                    $citoyen['prenom'],
                    $citoyen['date_naissance'],
                    $citoyen['lieu_naissance'],
                    $citoyen['url_photo_identite'] ?? null
                );
            }
        }
        return null;
    }

    public function save(Citoyen $citoyen): bool
    {
        $citoyens = $this->readData();

        $citoyens[] = $citoyen->toArray(); // Assure-toi que `Citoyen` a bien une méthode `toArray()`

        $this->writeData($citoyens);
        return true;
    }
}
