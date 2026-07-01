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
            'tendLavadas'   => $this->tendencia($lavada->contarHoy(), $lavada->contarAyer()),
            'tendIngresos'  => $this->tendencia($lavada->ingresosHoy(), $lavada->ingresosAyer()),
            'topClientes'   => $cliente->top(6),
            'porTipo'       => $lavada->ingresosPorTipo(),
            'porMes'        => $porMes,
            'mesActual'     => $mesActual,
        ]);
    }

    /**
     * Compara el valor de hoy contra el de ayer y devuelve la dirección y
     * el texto del indicador de tendencia ('up' | 'down' | 'flat').
     */
    private function tendencia(int $hoy, int $ayer): array
    {
        if ($ayer <= 0) {
            return $hoy > 0
                ? ['dir' => 'up', 'txt' => 'nuevo']
                : ['dir' => 'flat', 'txt' => 'sin datos de ayer'];
        }
        $pct = (int) round(($hoy - $ayer) / $ayer * 100);
        $dir = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');
        return ['dir' => $dir, 'txt' => ($pct > 0 ? '+' : '') . $pct . '% vs ayer'];
    }
}
