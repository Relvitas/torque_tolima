<?php
namespace App\Core;

/**
 * Controlador base con helpers comunes.
 */
abstract class Controller
{
    /** Renderiza una vista dentro del layout. */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        View::render($view, $data, $layout);
    }

    /** Responde JSON. */
    protected function json(array $data, int $status = 200): void
    {
        View::json($data, $status);
    }

    /** Redirige a una ruta interna. */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /** Obtiene un valor saneado de $_POST. */
    protected function input(string $key, $default = ''): string
    {
        return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
    }

    /** Obtiene un valor de $_GET. */
    protected function query(string $key, $default = ''): string
    {
        return isset($_GET[$key]) ? trim((string) $_GET[$key]) : $default;
    }

    /** Mensaje flash en sesión (para toasts tras redirect). */
    protected function flash(string $msg): void
    {
        $_SESSION['flash'] = $msg;
    }
}
