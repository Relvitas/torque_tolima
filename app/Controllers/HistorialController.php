<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lavada;

class HistorialController extends Controller
{
    public function index(): void
    {
        $q = $this->query('q');
        $this->view('historial/index', [
            'seccion'   => 'historial',
            'historial' => (new Lavada())->historial($q),
            'q'         => $q,
        ]);
    }

    public function eliminar(): void
    {
        $id = (int) $this->input('id', '0');
        if ($id > 0) {
            (new Lavada())->eliminar($id);
            $this->flash('Registro eliminado');
        }
        $this->redirect('/historial');
    }
}
