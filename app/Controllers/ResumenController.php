<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Lavada;

class ResumenController extends Controller
{
    public function index(): void
    {
        $lavada  = new Lavada();
        $cliente = new Cliente();

        // Garantiza que el mes en curso siempre aparezca (aunque sin lavadas).
        $mesActual = date('Y-m');
        $porMes    = $lavada->ingresosPorMes();
        if (!in_array($mesActual, array_column($porMes, 'mes'), true)) {
            array_unshift($porMes, ['mes' => $mesActual, 'cantidad' => 0, 'gratis' => 0, 'total' => 0]);
        }

        $this->view('resumen/index', [
            'seccion'       => 'resumen',
            'totalLavadas'  => $lavada->contar(),
            'totalClientes' => $cliente->contar(),
            'ingresos'      => $lavada->ingresosTotales(),
            'totalGratis'   => $lavada->totalGratis(),
            'topClientes'   => $cliente->top(6),
            'porTipo'       => $lavada->ingresosPorTipo(),
            'porMes'        => $porMes,
            'mesActual'     => $mesActual,
        ]);
    }
}
