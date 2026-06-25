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

    /** Muestra el formulario de edición de un cliente. */
    public function editar(string $id): void
    {
        $cliente = (new Cliente())->porId((int) $id);
        if (!$cliente) {
            $this->flash('Cliente no encontrado');
            $this->redirect('/clientes');
        }
        $this->view('clientes/editar', [
            'seccion' => 'clientes',
            'cliente' => $cliente,
        ]);
    }

    /** Guarda los cambios del cliente. */
    public function actualizar(): void
    {
        $id     = (int) $this->input('id', '0');
        $nombre = $this->input('nombre');
        $tel    = $this->input('telefono');
        $placa  = strtoupper($this->input('placa'));
        $moto   = $this->input('moto');

        if ($id <= 0) {
            $this->redirect('/clientes');
        }
        if ($nombre === '' || $tel === '') {
            $this->flash('El nombre y el teléfono son obligatorios');
            $this->redirect('/clientes/editar/' . $id);
        }

        $model = new Cliente();
        $otro  = $model->porTelefono($tel);
        if ($otro && (int) $otro['id'] !== $id) {
            $this->flash('Ya existe otro cliente con ese teléfono');
            $this->redirect('/clientes/editar/' . $id);
        }

        $model->actualizar($id, [
            'nombre'   => $nombre,
            'telefono' => $tel,
            'placa'    => $placa ?: null,
            'moto'     => $moto ?: null,
        ], $this->subirFoto());

        $this->flash('Cliente actualizado');
        $this->redirect('/clientes');
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

    /** Guarda la foto subida y devuelve el nombre del archivo (o null si no hay). */
    private function subirFoto(): ?string
    {
        if (empty($_FILES['foto']['tmp_name']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $info = getimagesize($_FILES['foto']['tmp_name']);
        if ($info === false) {
            return null;
        }
        $ext    = image_type_to_extension($info[2], false) ?: 'jpg';
        $nombre = 'moto_' . bin2hex(random_bytes(8)) . '.' . $ext;

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0775, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_PATH . '/' . $nombre);
        return $nombre;
    }
}
