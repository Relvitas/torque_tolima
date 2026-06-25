<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Egreso;

class EgresoController extends Controller
{
    /** Categorías disponibles para clasificar un egreso. */
    private const CATEGORIAS = ['Insumos', 'Servicios', 'Nómina', 'Arriendo', 'Equipos', 'Otros'];

    public function index(): void
    {
        $egreso = new Egreso();
        $mes    = $this->query('mes') ?: date('Y-m');

        $this->view('egresos/index', [
            'seccion'    => 'egresos',
            'egresos'    => $egreso->todos($mes),
            'totalMes'   => $egreso->totalMes($mes),
            'totalHoy'   => $egreso->totalHoy(),
            'porMes'     => $egreso->porMes(),
            'mes'        => $mes,
            'categorias' => self::CATEGORIAS,
        ]);
    }

    public function registrar(): void
    {
        $concepto  = $this->input('concepto');
        $monto     = (int) preg_replace('/\D/', '', $this->input('monto', '0'));
        $categoria = $this->input('categoria', 'Otros');
        $nota      = $this->input('nota');

        if ($concepto === '' || $monto <= 0) {
            $this->flash('Ingresa un concepto y un monto válido');
            $this->redirect('/egresos');
        }

        if (!in_array($categoria, self::CATEGORIAS, true)) {
            $categoria = 'Otros';
        }

        (new Egreso())->crear([
            'concepto'  => $concepto,
            'categoria' => $categoria,
            'monto'     => $monto,
            'nota'      => $nota,
        ]);
        $this->flash('Egreso registrado');
        $this->redirect('/egresos');
    }

    public function eliminar(): void
    {
        $id = (int) $this->input('id', '0');
        if ($id > 0) {
            (new Egreso())->eliminar($id);
            $this->flash('Egreso eliminado');
        }
        $this->redirect('/egresos');
    }
}
