<?php 
namespace App\Entity;

use App\Core\Abstract\AbstractEntity;
use DateTime;

class Citoyen extends AbstractEntity
{
    private int  $id;
    private string $nom;
    private string $prenom;
    private string $cni;
    private DateTime $dateNaissance;
    private string $lieuNaissance;
    private string $photoCniRecto;
    private string $photoCniVerso;

    public function __construct(int $id = 0, string $nom = "", string $prenom = "", string $cni = "", DateTime $dateNaissance = new DateTime(), string $lieuNaissance = "", string $photoCniRecto = "", string $photoCniVerso = "")
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->cni = $cni;
        $this->dateNaissance = $dateNaissance;
        $this->lieuNaissance = $lieuNaissance;
        $this->photoCniRecto = $photoCniRecto;
        $this->photoCniVerso = $photoCniVerso;
    }
      

    public function getId(): int
    {
        return $this->id;
    }
    public function getNom(): string
    {
        return $this->nom;
    }
    public function getPrenom(): string
    {
        return $this->prenom;
    }
    public function getCni(): string
    {
        return $this->cni;
    }
    public function getDateNaissance(): DateTime

    {
        return $this->dateNaissance;
    }
    public function getLieuNaissance(): string
    {
        return $this->lieuNaissance;
    }
    public function getPhotoCniRecto(): string
    {
        return $this->photoCniRecto;
    }
    public function getPhotoCniVerso(): string
    {
        return $this->photoCniVerso;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
    public function setCni(string $cni): void
    {
        $this->cni = $cni;
       
    }
    public function setDateNaissance(DateTime $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }
    public function setLieuNaissance(string $lieuNaissance): void
    {
        $this->lieuNaissance = $lieuNaissance;
    }
    public function setPhotoCniRecto(string $photoCniRecto): void
    {
        $this->photoCniRecto = $photoCniRecto;
    }
    public function setPhotoCniVerso(string $photoCniVerso): void
    {
        $this->photoCniVerso = $photoCniVerso;
    
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'cni' => $this->cni,
            'dateNaissance' => $this->dateNaissance->format('Y-m-d'),
            'lieuNaissance' => $this->lieuNaissance,
            'photoCniRecto' => $this->photoCniRecto,
            'photoCniVerso' => $this->photoCniVerso
        ];
    }

    public static function toObject(array $tableau): static{
        $citoyen = new static();
        $citoyen->setId($tableau['id'] ?? 0);
        $citoyen->setNom($tableau['nom'] ?? '');
        $citoyen->setPrenom($tableau['prenom'] ?? '');
        $citoyen->setCni($tableau['cni'] ?? '');
        $citoyen->setDateNaissance(new DateTime($tableau['dateNaissance'] ?? 'now'));
        $citoyen->setLieuNaissance($tableau['lieuNaissance'] ?? '');
        $citoyen->setPhotoCniRecto($tableau['photoCniRecto'] ?? '');
        $citoyen->setPhotoCniVerso($tableau['photoCniVerso'] ?? '');

        return $citoyen;
    }

}