<?php


namespace App\Repository;
use App\Entity\Citoyen;


interface ICitoyenRepository
{
    public function findByNci(string $nci): ?Citoyen;
    public function save(Citoyen $citoyen): bool;
    public function findAll(): array;



}
