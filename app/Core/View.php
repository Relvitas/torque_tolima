<?php
namespace App\Core;

/**
 * Renderizador de vistas. Envuelve la vista dentro de un layout.
 */
class View
{
    /**
     * Renderiza una vista dentro de un layout.
     *
     * @param string $view   Ruta relativa sin extensión, ej: 'lavada/index'
     * @param array  $data    Variables disponibles en la vista
     * @param string $layout Layout a usar (vacío = sin layout)
     */
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            exit("Vista no encontrada: {$view}");
        }

        // Captura el contenido de la vista en $content.
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === '') {
            echo $content;
            return;
        }

        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }

    /** Devuelve JSON y termina la ejecución. */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
