<?php
/**
 * Front Controller — punto de entrada único de la aplicación.
 * Todas las peticiones pasan por aquí (ver .htaccess).
 */

session_start();

require __DIR__ . '/../config/config.php';
require APP_PATH . '/Core/helpers.php';

// --- Autoloader PSR-4 simple para el namespace App\ ---
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = APP_PATH . '/' . $relative . '.php';
    if (is_file($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Controllers\LavadaController;
use App\Controllers\CitaController;
use App\Controllers\ClienteController;
use App\Controllers\HistorialController;
use App\Controllers\ResumenController;
use App\Controllers\FacturaController;

$router = new Router();

// --- Rutas ---
$router->get('/',                  [LavadaController::class,    'index']);
$router->get('/lavada/buscar',     [LavadaController::class,    'buscar']);
$router->post('/lavada/registrar', [LavadaController::class,    'registrar']);

$router->get('/citas',             [CitaController::class,      'index']);
$router->post('/citas/agendar',    [CitaController::class,      'agendar']);
$router->post('/citas/eliminar',   [CitaController::class,      'eliminar']);

$router->get('/clientes',          [ClienteController::class,   'index']);
$router->post('/clientes/eliminar',[ClienteController::class,   'eliminar']);
$router->get('/historial',         [HistorialController::class, 'index']);
$router->post('/historial/eliminar',[HistorialController::class,'eliminar']);
$router->get('/resumen',           [ResumenController::class,   'index']);

$router->get('/factura/{id}',      [FacturaController::class,   'show']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
