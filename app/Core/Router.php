<?php
namespace App\Core;

/**
 * Router minimalista. Soporta rutas con parámetros tipo {id}.
 */
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }

    /** Resuelve la petición actual y despacha al controlador. */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        // Descarta el prefijo del subdirectorio (BASE_URL) si lo hay.
        if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }
        $uri = $this->normalize($path);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Convierte {param} en grupos de captura.
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // descarta coincidencia completa
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->$action(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo '404 — Página no encontrada';
    }
}
