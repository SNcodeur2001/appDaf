<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;
use App\Service\CitoyenService;

class CitoyenController extends AbstractController
{
    private CitoyenService $citoyenService;

    public function __construct(CitoyenService $citoyenService)
    {
        parent::__construct();
        $this->citoyenService = $citoyenService;
    }

   
    public function index(): void
    {
        try {
            $citoyens = $this->citoyenService->getAllCitoyens();
            $data = array_map(fn($citoyen) => $citoyen->toArray(), $citoyens);
            
            $this->renderJson(
                $data,
                "success",
                200,
                "Liste des citoyens récupérée avec succès"
            );
        } catch (\Exception $e) {
            $this->renderJson(
                null,
                "error",
                500,
                "Erreur lors de la récupération des citoyens: " . $e->getMessage()
            );
        }
    }

    /**
     * POST /api/citoyens - Crée un nouveau citoyen
     */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->renderJson(
                    null,
                    "error",
                    400,
                    "Données JSON invalides"
                );
                return;
            }

            // Validation des données requises
            $requiredFields = ['nci', 'nom', 'prenom', 'date_naissance', 'lieu_naissance'];
            foreach ($requiredFields as $field) {
                if (empty($input[$field])) {
                    $this->renderJson(
                        null,
                        "error",
                        400,
                        "Le champ '$field' est requis"
                    );
                    return;
                }
            }

            $citoyen = $this->citoyenService->createCitoyen($input);
            
            $this->renderJson(
                $citoyen->toArray(),
                "success",
                201,
                "Citoyen créé avec succès"
            );
        } catch (\InvalidArgumentException $e) {
            $this->renderJson(
                null,
                "error",
                400,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->renderJson(
                null,
                "error",
                500,
                "Erreur lors de la création du citoyen: " . $e->getMessage()
            );
        }
    }

    /**
     * GET /api/citoyen/{nci} - Recherche un citoyen par NCI
     */
    public function show(): void
    {
        try {
            // Récupérer le NCI depuis l'URL ou les paramètres
            $nci = $_GET['nci'] ?? null;
            
            if (!$nci) {
                $this->renderJson( null, "error", 400, "Le paramètre NCI est requis");
                return;
            }

            // Validation du format NCI
            if (empty(trim($nci))) {
                $this->renderJson(
                    null,
                    "error",
                    400,
                    "Le NCI ne peut pas être vide"
                );
                return;
            }

            $citoyen = $this->citoyenService->findCitoyenByNci($nci);
            
            if ($citoyen) {
                $this->renderJson(
                    $citoyen->toArray(),
                    "success",
                    200,
                    "Le numéro de carte d'identité a été retrouvé"
                );
            } else {
                $this->renderJson(
                    null,
                    "error",
                    404,
                    "Le numéro de carte d'identité non retrouvé"
                );
            }
        } catch (\InvalidArgumentException $e) {
            $this->renderJson(
                null,
                "error",
                400,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->renderJson(
                null,
                "error",
                500,
                "Erreur lors de la recherche: " . $e->getMessage()
            );
        }
    }

    /**
     * Méthode pour rechercher par NCI via URL path parameter
     * GET /api/citoyen/nci/{nci}
     */
    public function findByNci(string $nci): void
    {
        try {
            if (empty($nci)) {
                $this->renderJson(
                    null,
                    "error",
                    400,
                    "Le paramètre NCI est requis"
                );
                return;
            }

            $citoyen = $this->citoyenService->findCitoyenByNci($nci);
            
            if ($citoyen) {
                $this->renderJson(
                    $citoyen->toArray(),
                    "success",
                    200,
                    "Le numéro de carte d'identité a été retrouvé"
                );
            } else {
                $this->renderJson(
                    null,
                    "error",
                    404,
                    "Le numéro de carte d'identité non retrouvé"
                );
            }
        } catch (\Exception $e) {
            $this->renderJson(
                null,
                "error",
                500,
                "Erreur lors de la recherche: " . $e->getMessage()
            );
        }
    }

    public function create(): void
    {
        // Non utilisé pour l'API REST
        $this->renderJson(
            null,
            "error",
            405,
            "Méthode non autorisée"
        );
    }

    public function destroy(): void
    {
        // Non implémenté pour cette version
        $this->renderJson(
            null,
            "error",
            405,
            "Méthode non autorisée"
        );
    }

    public function edit(): void
    {
        // Non utilisé pour l'API REST
        $this->renderJson(
            null,
            "error",
            405,
            "Méthode non autorisée"
        );
    }
}