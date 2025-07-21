<?php

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static bool $routesLoaded = false;

    public static function get(string $uri, string $controller, string $action, array $middlewares = []): void
    {
        self::$routes['GET'][$uri] = [
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public static function post(string $uri, string $controller, string $action, array $middlewares = []): void
    {
        self::$routes['POST'][$uri] = [
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public static function resolve(): void
    {
        // Charger les routes si pas encore fait
        if (!self::$routesLoaded) {
            self::loadRoutes();
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset(self::$routes[$method][$uri])) {
            $route = self::$routes[$method][$uri];

            // ✨ Exécuter les middlewares AVANT le contrôleur
            if (!empty($route['middlewares'])) {
                self::runMiddlewares($route['middlewares']);
            }

            $controllerName = $route['controller'];
            $action = $route['action'];

            $controller = new $controllerName();
            $controller->$action();
        } else {
            // 404 - Route non trouvée
            http_response_code(404);
            require_once dirname(__DIR__, 2) . '/templates/404.php';
        }
    }

    /**
     * ✨ Exécuter les middlewares avec chargement manuel
     */
    private static function runMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middlewareName) {
            switch ($middlewareName) {
                case 'auth':
                    self::runAuthMiddleware();
                    break;
                case 'guest':
                    self::runGuestMiddleware();
                    break;
                default:
                    throw new \Exception("Middleware '$middlewareName' non supporté.");
            }
        }
    }

    /**
     * Middleware d'authentification
     */
    private static function runAuthMiddleware(): void
    {
        // Démarrer la session si pas encore fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }

        // Vérifier que les données utilisateur sont complètes
        if (!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id'])) {
            session_destroy();
            header('Location: /');
            exit();
        }

        // Vérifier le statut du compte
        if (isset($_SESSION['user']['statut_compte']) && 
            $_SESSION['user']['statut_compte'] !== 'ACTIF') {
            session_destroy();
            header('Location: /?error=compte_inactif');
            exit();
        }
    }

    /**
     * Middleware pour les invités (non connectés)
     */
    private static function runGuestMiddleware(): void
    {
        // Démarrer la session si pas encore fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si l'utilisateur est déjà connecté, rediriger vers le dashboard
        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            exit();
        }
    }

    private static function loadRoutes(): void
    {
        require_once dirname(__DIR__, 2) . '/routes/route.web.php';
        self::$routesLoaded = true;
    }
}