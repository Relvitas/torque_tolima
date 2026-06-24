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

        $this->view('resumen/index', [
            'seccion'       => 'resumen',
            'totalLavadas'  => $lavada->contar(),
            'totalClientes' => $cliente->contar(),
            'ingresos'      => $lavada->ingresosTotales(),
            'totalGratis'   => $lavada->totalGratis(),
            'topClientes'   => $cliente->top(6),
            'porTipo'       => $lavada->ingresosPorTipo(),
        ]);
    }
}
