<?php

namespace App\Entity;

class Citoyen
{
    private ?int $id;
    private string $nci;
    private string $nom;
    private string $prenom;
    private string $dateNaissance;
    private string $lieuNaissance;
    private ?string $urlPhotoIdentite;
    private ?\DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        string $nci,
        string $nom,
        string $prenom,
        string $dateNaissance,
        string $lieuNaissance,
        ?string $urlPhotoIdentite = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nci = $nci;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->lieuNaissance = $lieuNaissance;
        $this->urlPhotoIdentite = $urlPhotoIdentite;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNci(): string
    {
        return $this->nci;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getDateNaissance(): string
    {
        return $this->dateNaissance;
    }

    public function getLieuNaissance(): string
    {
        return $this->lieuNaissance;
    }

    public function getUrlPhotoIdentite(): ?string
    {
        return $this->urlPhotoIdentite;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNci(string $nci): void
    {
        $this->nci = $nci;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setDateNaissance(string $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): void
    {
        $this->lieuNaissance = $lieuNaissance;
    }

    public function setUrlPhotoIdentite(?string $urlPhotoIdentite): void
    {
        $this->urlPhotoIdentite = $urlPhotoIdentite;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Méthode pour convertir en array (utile pour l'API JSON)
    public function toArray(): array
    {
        return [
            'nci' => $this->nci,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date' => $this->dateNaissance,
            'lieu' => $this->lieuNaissance,
            'url_photo_identite' => $this->urlPhotoIdentite
        ];
    }
}
