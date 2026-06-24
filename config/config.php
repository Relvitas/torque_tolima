<?php
/**
 * Configuración global de la aplicación.
 * Ajusta las credenciales de la base de datos según tu entorno.
 */

// --- Errores (desactiva display_errors en producción) ---
error_reporting(E_ALL);
ini_set('display_errors', '1');

// --- Base de datos MySQL / MariaDB ---
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'torque_tolima');
define('DB_USER', getenv('DB_USER') ?: 'torque');
define('DB_PASS', getenv('DB_PASS') ?: 'torque2024');
define('DB_CHARSET', 'utf8mb4');

// --- Rutas base ---
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

// URL base del sitio (ajusta si lo sirves bajo un subdirectorio).
define('BASE_URL', rtrim(getenv('BASE_URL') ?: '', '/'));

// --- Negocio ---
define('WA_NUM', '573153589152');          // WhatsApp del negocio
define('LAVADAS_PARA_GRATIS', 6);            // Cada 6.ª lavada es gratis
