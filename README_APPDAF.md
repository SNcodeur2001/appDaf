# AppDAF - Simulation de l'Application DAF

## Description
AppDAF est une application qui simule l'application de la DAF (Direction de l'Automatisation des Fichiers). Elle permet de récupérer les informations d'un citoyen à partir de son NCI (Numéro de Carte d'Identité).

## Fonctionnalités
- Recherche d'un citoyen par NCI
- API REST avec réponses JSON
- Journalisation automatique des requêtes
- Stockage des URL des photos d'identité (cloud)
- Dockerisé avec PostgreSQL

## Technologies Utilisées
- **Langage** : PHP 8+ Orienté Objet
- **API** : REST (JSON)
- **Base de données** : PostgreSQL
- **Stockage** : Cloud (URLs stockées en BD)
- **Serveur Web** : NGINX
- **Conteneurisation** : Docker
- **Déploiement** : Render

## Installation et Configuration

### Prérequis
- Docker et Docker Compose
- PHP 8.0+
- Composer

### Installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd appDaf
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
# Modifier les variables selon votre configuration
```

4. **Démarrer les services Docker**
```bash
docker-compose up -d
```

5. **Exécuter les migrations**
```bash
composer run database:migrate -- --reset
```

6. **Insérer les données de test**
```bash
composer run seeder:migrate -- --reset
```

## Utilisation de l'API

### Base URL
```
http://localhost:8083
```

### Endpoints

#### 1. Vérification de l'état de l'API
```http
GET /health
```

**Réponse:**
```json
{
  "data": {
    "status": "ok",
    "timestamp": "2024-01-20 10:30:45"
  },
  "statut": "success",
  "code": 200,
  "message": "AppDAF API is running"
}
```

#### 2. Rechercher un citoyen par NCI (URL Parameter)
```http
GET /citoyen/nci/{nci}
```

**Exemple:**
```http
GET /citoyen/nci/1987654321012
```

#### 3. Rechercher un citoyen par NCI (Query Parameter)
```http
GET /citoyen?nci={nci}
```

**Exemple:**
```http
GET /citoyen?nci=1987654321012
```

#### 4. Lister tous les citoyens
```http
GET /citoyens
```

#### 5. Créer un nouveau citoyen
```http
POST /citoyens
Content-Type: application/json

{
  "nci": "1234567890123",
  "nom": "DIOP",
  "prenom": "Mamadou",
  "date_naissance": "1990-01-15",
  "lieu_naissance": "Dakar",
  "url_photo_identite": "https://cloud-storage.com/photos/1234567890123.jpg"
}
```

### Structure des Réponses

#### Réponse Success
```json
{
  "data": {
    "nci": "1987654321012",
    "nom": "DIOP",
    "prenom": "Aminata",
    "date": "1990-03-15",
    "lieu": "Dakar",
    "url_photo_identite": "https://example-cloud.com/photos/1987654321012.jpg"
  },
  "statut": "success",
  "code": 200,
  "message": "Le numéro de carte d'identité a été retrouvé"
}
```

#### Réponse Error 
```json
{
  "data": null,
  "statut": "error",
  "code": 404,
  "message": "Le numéro de carte d'identité non retrouvé"
}
```

## Données de Test

L'application inclut les citoyens de test suivants :

| NCI | Nom | Prénom | Lieu de Naissance |
|-----|-----|--------|-------------------|
| 1987654321012 | DIOP | Aminata | Dakar |
| 1876543210987 | FALL | Moussa | Saint-Louis |
| 1765432109876 | NDIAYE | Fatou | Thiès |
| 1654321098765 | SALL | Ibrahima | Kaolack |
| 1543210987654 | BA | Awa | Ziguinchor |

## Journalisation

Toutes les requêtes sont automatiquement journalisées avec :
- Date et heure
- Adresse IP
- Localisation (si disponible)
- Statut (Success/Échec)
- NCI recherché
- Temps de réponse

## Commandes Utiles

```bash
# Démarrer l'application
composer run start

# Réinitialiser la base de données
composer run database:migrate -- --reset

# Insérer les données de test
composer run seeder:migrate -- --reset

# Voir les logs Docker
docker-compose logs -f

# Accéder à la base de données
docker exec -it postgresDaf psql -U pguserDaf -d pgdbDaf
```

## Tests avec REST Client

Vous pouvez tester l'API avec l'extension REST Client de VS Code :

```http
### Test de santé
GET http://localhost:8081/health

### Recherche par NCI
GET http://localhost:8081/citoyen/nci/1987654321012

### Recherche avec query parameter
GET http://localhost:8081/citoyen?nci=1876543210987

### Créer un nouveau citoyen
POST http://localhost:8081/citoyens
Content-Type: application/json

{
  "nci": "1111111111111",
  "nom": "TEST",
  "prenom": "Utilisateur",
  "date_naissance": "1995-05-05",
  "lieu_naissance": "Dakar",
  "url_photo_identite": "https://example.com/photo.jpg"
}
```

## Critères d'Acceptation

✅ Toutes les demandes sont journalisées (date, heure, localisation, IP, statut)
✅ Les photos d'identité sont stockées dans le cloud (URLs en BD)
✅ Les contrôleurs retournent et reçoivent du JSON
✅ Structure de réponse conforme aux spécifications
✅ Architecture orientée objet avec separation des responsabilités
