<?php

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static bool $routesLoaded = false;

    public static function get(string $path, string $controller = '', string $action = '', array $middlewares = [], $handler = null): void
    {
        self::$routes['GET'][$path] = compact('controller', 'action', 'middlewares', 'handler');
    }

    public static function post(string $path, string $controller = '', string $action = '', array $middlewares = [], $handler = null): void
    {
        self::$routes['POST'][$path] = compact('controller', 'action', 'middlewares', 'handler');
    }

public static function resolve(): void
{
    if (!self::$routesLoaded) {
        self::loadRoutes();
    }

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];

    foreach (self::$routes[$method] ?? [] as $routePath => $route) {
        $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $routePath);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // on retire le match complet

            if (!empty($route['middlewares'])) {
                self::runMiddlewares($route['middlewares']);
            }

            // Handler anonyme ?
            if (isset($route['handler']) && is_callable($route['handler'])) {
                call_user_func_array($route['handler'], $matches);
                return;
            }

            $controllerName = $route['controller'];
            $action = $route['action'];

            if (!class_exists($controllerName)) {
                self::respondNotFound("Contrôleur '$controllerName' introuvable.");
                return;
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $action)) {
                self::respondNotFound("Méthode '$action' introuvable dans le contrôleur.");
                return;
            }

            call_user_func_array([$controller, $action], $matches);
            return;
        }
    }

    self::respondNotFound("Endpoint non trouvé");
}


    private static function respondNotFound(string $message = 'Endpoint non trouvé'): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 404,
            'message' => $message
        ]);
    }

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

    private static function runAuthMiddleware(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            session_destroy();
            header('Location: /');
            exit();
        }

        if (isset($_SESSION['user']['statut_compte']) && $_SESSION['user']['statut_compte'] !== 'ACTIF') {
            session_destroy();
            header('Location: /?error=compte_inactif');
            exit();
        }
    }

    private static function runGuestMiddleware(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            exit();
        }
    }

    private static function loadRoutes(): void
    {
        require_once dirname(__DIR__, 2) . '/routes/routes.php';
        self::$routesLoaded = true;
    }
}
