<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Retire le base path si nécessaire
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes[$method] ?? [] as $route => $action) {
            $pattern = preg_replace('/\{[a-z]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // retire le match complet
                [$controllerName, $methodName] = explode('@', $action);

                $controllerFile = ROOT . "/app/Controllers/{$controllerName}.php";
                if (!file_exists($controllerFile)) {
                    $this->abort(500, "Contrôleur introuvable : {$controllerName}");
                    return;
                }

                require_once $controllerFile;
                $controller = new $controllerName();
                $controller->$methodName(...$matches);
                return;
            }
        }

        $this->abort(404, 'Page introuvable');
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>{$code}</h1><p>{$message}</p>";
    }
}
