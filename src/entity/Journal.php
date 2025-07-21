<?php
namespace App\Entity;

use App\Core\Abstract\AbstractEntity;
use DateTime;

class Journal extends AbstractEntity
{
    private int $id;
    private DateTime $date;
    private int $heure;
    private string $ipAdresse;
    private string $localisation;
    private StatutEnum $status;
    private string $codeHttp;
    private Citoyen $citoyen;

  public function __construct(int $id, DateTime $date, int $heure, string $ipAdresse, string $localisation, StatutEnum $status, string $codeHttp)
    {
        $this->id = $id;
        $this->date = $date;
        $this->heure = $heure;
        $this->ipAdresse = $ipAdresse;
        $this->localisation = $localisation;
        $this->status = $status;
        $this->codeHttp = $codeHttp;
        $this->citoyen = new Citoyen();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function getHeure(): int
    {
        return $this->heure;
    }
    public function getIpAdresse(): string
    {
        return $this->ipAdresse;
    }
    public function getLocalisation(): string
    {
        return $this->localisation;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function getCodeHttp(): string
    {
        return $this->codeHttp;
    }

    public function getCitoyen(): Citoyen
    {
        return $this->citoyen;
    }

    public function setCitoyen(Citoyen $citoyen): void
    {
        $this->citoyen = $citoyen;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function setHeure(int $heure): void
    {
        $this->heure = $heure;
    }

    public function setIpAdresse(string $ipAdresse): void
    {
        $this->ipAdresse = $ipAdresse;
    }

    public function setLocalisation(string $localisation): void
    {
        $this->localisation = $localisation;
    }

    public function setStatus(StatutEnum $status): void
    {
        $this->status = $status;
    }

    public function setCodeHttp(string $codeHttp): void
    {
        $this->codeHttp = $codeHttp;
    }

        public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'heure' => $this->heure,
            'ipAdresse' => $this->ipAdresse,
            'localisation' => $this->localisation,
            'status' => $this->status->value,
            'codeHttp' => $this->codeHttp,
            'citoyen' => $this->citoyen->toArray()
        ];
    }

    public static function toObject(array $tableau): static{
        $journal = new static(
            $tableau['id'] ?? 0,
            new DateTime($tableau['date'] ?? 'now'),
            $tableau['heure'] ?? 0,
            $tableau['ipAdresse'] ?? '',
            $tableau['localisation'] ?? '',
            StatutEnum::from($tableau['status'] ?? StatutEnum::Success->value),
            $tableau['codeHttp'] ?? ''
        );

        if (isset($tableau['citoyen'])) {
            $journal->setCitoyen(Citoyen::toObject($tableau['citoyen']));
        }

        return $journal;
    }
    
}