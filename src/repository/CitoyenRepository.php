<?php

namespace App\Repository;

use App\Core\Abstract\AbstractRepository;
use App\Entity\Citoyen;
use PDO;
use PDOException;

class CitoyenRepository extends AbstractRepository
{

    public function findByNci(string $nci): ?Citoyen
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite, created_at, updated_at 
                FROM citoyens 
                WHERE nci = :nci
            ");
            $stmt->bindParam(':nci', $nci, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            $reflection = new \ReflectionClass(Citoyen::class);
            $citoyen = $reflection->newInstance(
                $result['nci'],
                $result['nom'],
                $result['prenom'],
                $result['date_naissance'],
                $result['lieu_naissance'],
                $result['url_photo_identite'],
                $result['id']
            );

            return $citoyen;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la recherche du citoyen: " . $e->getMessage());
        }
    }

    public function save(Citoyen $citoyen): bool
    {
        try {
            if ($citoyen->getId() === null) {
                // Insertion
                $stmt = $this->pdo->prepare("
                    INSERT INTO citoyens (nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite) 
                    VALUES (:nci, :nom, :prenom, :date_naissance, :lieu_naissance, :url_photo_identite)
                ");
            } else {
                // Mise à jour
                $stmt = $this->pdo->prepare("
                    UPDATE citoyens 
                    SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, 
                        lieu_naissance = :lieu_naissance, url_photo_identite = :url_photo_identite, 
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $citoyen->getId(), PDO::PARAM_INT);
            }

            $nci = $citoyen->getNci();
            $nom = $citoyen->getNom();
            $prenom = $citoyen->getPrenom();
            $dateNaissance = $citoyen->getDateNaissance();
            $lieuNaissance = $citoyen->getLieuNaissance();
            $urlPhoto = $citoyen->getUrlPhotoIdentite();
            
            $stmt->bindParam(':nci', $nci, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':date_naissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindParam(':lieu_naissance', $lieuNaissance, PDO::PARAM_STR);
            $stmt->bindParam(':url_photo_identite', $urlPhoto, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la sauvegarde du citoyen: " . $e->getMessage());
        }
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT id, nci, nom, prenom, date_naissance, lieu_naissance, url_photo_identite 
                FROM citoyens 
                ORDER BY created_at DESC
            ");
            
            $citoyens = [];
            while ($result = $stmt->fetch()) {
                $reflection = new \ReflectionClass(Citoyen::class);
                $citoyens[] = $reflection->newInstance(
                    $result['nci'],
                    $result['nom'],
                    $result['prenom'],
                    $result['date_naissance'],
                    $result['lieu_naissance'],
                    $result['url_photo_identite'],
                    $result['id']
                );
            }

            return $citoyens;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la récupération des citoyens: " . $e->getMessage());
        }
    }
}
