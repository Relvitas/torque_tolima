<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(): void
    {
        $q = $this->query('q');
        $this->view('clientes/index', [
            'seccion'  => 'clientes',
            'clientes' => (new Cliente())->buscar($q),
            'q'        => $q,
            'meta'     => LAVADAS_PARA_GRATIS,
        ]);
    }

    public function eliminar(): void
    {
        $id = (int) $this->input('id', '0');
        if ($id > 0) {
            (new Cliente())->eliminar($id);
            $this->flash('Cliente eliminado');
        }
        $this->redirect('/clientes');
    }
}
