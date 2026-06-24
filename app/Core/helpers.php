<?php
/**
 * Funciones de ayuda globales para las vistas.
 */

if (!function_exists('e')) {
    /** Escapa texto para HTML (previene XSS). */
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /** Construye una URL interna respetando BASE_URL. */
    function url(string $path = ''): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('cop')) {
    /** Formatea un entero como pesos colombianos: 15000 -> $15.000 */
    function cop($value): string
    {
        return '$' . number_format((int) $value, 0, ',', '.');
    }
}

if (!function_exists('inicial')) {
    /** Primera letra en mayúscula de un texto (seguro con o sin mbstring). */
    function inicial(string $texto): string
    {
        $texto = trim($texto);
        if ($texto === '') {
            return '?';
        }
        if (function_exists('mb_substr')) {
            return mb_strtoupper(mb_substr($texto, 0, 1, 'UTF-8'), 'UTF-8');
        }
        return strtoupper(substr($texto, 0, 1));
    }
}

if (!function_exists('is_active')) {
    /** Devuelve 'active' si la sección actual coincide. */
    function is_active(string $section, string $current): string
    {
        return $section === $current ? 'active' : '';
    }
}
