<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Egreso;
use App\Models\Lavada;

class ResumenController extends Controller
{
    public function index(): void
    {
        $lavada  = new Lavada();
        $cliente = new Cliente();
        $egreso  = new Egreso();

        $mesActual = date('Y-m');

        // Combina ingresos (lavadas) y egresos por mes para calcular la utilidad neta.
        $egresosMap = [];
        foreach ($egreso->porMes() as $e) {
            $egresosMap[$e['mes']] = (int) $e['total'];
        }

        $porMes = [];
        foreach ($lavada->ingresosPorMes() as $m) {
            $eg = $egresosMap[$m['mes']] ?? 0;
            $porMes[$m['mes']] = [
                'mes'      => $m['mes'],
                'cantidad' => (int) $m['cantidad'],
                'gratis'   => (int) $m['gratis'],
                'total'    => (int) $m['total'],
                'efectivo' => (int) $m['efectivo'],
                'nequi'    => (int) $m['nequi'],
                'egresos'  => $eg,
                'neto'     => (int) $m['total'] - $eg,
            ];
        }
        // Meses que solo tienen egresos (sin lavadas) también deben aparecer.
        foreach ($egresosMap as $mes => $eg) {
            if (!isset($porMes[$mes])) {
                $porMes[$mes] = [
                    'mes'      => $mes,
                    'cantidad' => 0,
                    'gratis'   => 0,
                    'total'    => 0,
                    'efectivo' => 0,
                    'nequi'    => 0,
                    'egresos'  => $eg,
                    'neto'     => -$eg,
                ];
            }
        }
        // Garantiza que el mes en curso siempre aparezca (aunque sin movimientos).
        if (!isset($porMes[$mesActual])) {
            $porMes[$mesActual] = ['mes' => $mesActual, 'cantidad' => 0, 'gratis' => 0, 'total' => 0, 'efectivo' => 0, 'nequi' => 0, 'egresos' => 0, 'neto' => 0];
        }
        // Ordena por mes descendente (más reciente primero).
        krsort($porMes);
        $porMes = array_values($porMes);

        $this->view('resumen/index', [
            'seccion'       => 'resumen',
            'lavadasHoy'    => $lavada->contarHoy(),
            'totalClientes' => $cliente->contar(),
            'ingresos'      => $lavada->ingresosHoy(),
            'totalGratis'   => $lavada->totalGratis(),
            'topClientes'   => $cliente->top(6),
            'porTipo'       => $lavada->ingresosPorTipo(),
            'porMes'        => $porMes,
            'mesActual'     => $mesActual,
        ]);
    }
}
