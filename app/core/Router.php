<?php
namespace App\Core;

class Router
{
    private static array $routes = [];
    private static bool $routesLoaded = false;

    public static function get(string $uri, $controllerOrCallback, ?string $action = null, array $middlewares = []): void
    {
        self::addRoute('GET', $uri, $controllerOrCallback, $action, $middlewares);
    }

    public static function post(string $uri, $controllerOrCallback, ?string $action = null, array $middlewares = []): void
    {
        self::addRoute('POST', $uri, $controllerOrCallback, $action, $middlewares);
    }

    private static function addRoute(string $method, string $uri, $controllerOrCallback, ?string $action, array $middlewares): void
    {
        if (is_callable($controllerOrCallback)) {
            self::$routes[$method][$uri] = [
                'callback' => $controllerOrCallback,
                'middlewares' => $middlewares
            ];
        } else {
            self::$routes[$method][$uri] = [
                'controller' => $controllerOrCallback,
                'action' => $action,
                'middlewares' => $middlewares
            ];
        }
    }

    public static function resolve(): void
    {
        if (!self::$routesLoaded) {
            self::loadRoutes();
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes[$method] ?? [] as $routeUri => $route) {
            $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([^/]+)', $routeUri);

            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);

                if (!empty($route['middlewares'])) {
                    self::runMiddlewares($route['middlewares']);
                }

                header('Content-Type: application/json');

                // Cas 1 : Callback direct
                if (isset($route['callback'])) {
                    call_user_func_array($route['callback'], $matches);
                    return;
                }

                // Cas 2 : Contrôleur + action
                $controllerName = $route['controller'] ?? null;
                $action = $route['action'] ?? null;

                if (!$controllerName || !$action) {
                    http_response_code(500);
                    echo json_encode(['message' => 'Route mal configurée']);
                    return;
                }

                // Injection manuelle si besoin
                switch ($controllerName) {
                    case 'App\Controller\CitoyenController':
                        $repository = new \App\Repository\CitoyenRepository();
                        $logger = new \App\Service\LoggerService();
                        $service = new \App\Service\CitoyenService($repository, $logger);
                        $controller = new $controllerName($service);
                        break;

                    default:
                        $controller = new $controllerName();
                        break;
                }

                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        // Aucun match
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 404,
            'message' => 'Endpoint non trouvé'
        ]);
    }

    private static function loadRoutes(): void
    {
        require_once __DIR__ . '/../routes/routes.php';
        self::$routesLoaded = true;
    }

    private static function runMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            (new $middleware())->handle();
        }
    }
}
