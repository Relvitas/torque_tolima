<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Lavada;

class LavadaController extends Controller
{
    /** Pantalla principal: formulario de nueva lavada. */
    public function index(): void
    {
        $lavada = new Lavada();
        $this->view('lavada/index', [
            'seccion'      => 'nueva',
            'telPrefill'   => $this->query('tel'),
            'lavadasHoy'   => $lavada->deHoy(),
            'totalHoy'     => $lavada->ingresosHoy(),
        ]);
    }

    /** Endpoint JSON para autocompletar al escribir el teléfono. */
    public function buscar(): void
    {
        $tel = $this->query('tel');
        if (strlen($tel) < 7) {
            $this->json(['encontrado' => false]);
        }
        $cliente = (new Cliente())->porTelefono($tel);
        if (!$cliente) {
            $this->json(['encontrado' => false]);
        }
        $ciclo = $cliente['lavadas'] % LAVADAS_PARA_GRATIS;
        $this->json([
            'encontrado' => true,
            'cliente'    => [
                'nombre'  => $cliente['nombre'],
                'placa'   => $cliente['placa'],
                'moto'    => $cliente['moto'],
                'foto'    => $cliente['foto'] ? url('uploads/' . $cliente['foto']) : null,
                'lavadas' => (int) $cliente['lavadas'],
                'ciclo'   => $ciclo,
            ],
        ]);
    }

    /** Registra una nueva lavada. */
    public function registrar(): void
    {
        $tel    = $this->input('telefono');
        $nombre = $this->input('nombre');
        $placa  = strtoupper($this->input('placa'));
        $moto   = $this->input('tipoMoto');
        $precio = (int) $this->input('precio', '0');

        if ($tel === '' || $nombre === '') {
            $this->flash('Por favor ingresa teléfono y nombre');
            $this->redirect('/');
        }
        if ($precio <= 0) {
            $this->flash('Selecciona el tipo de lavada');
            $this->redirect('/');
        }

        $foto = $this->subirFoto();

        $clienteModel = new Cliente();
        $cliente = $clienteModel->guardarDatos($tel, $nombre, $placa, $moto, $foto);

        // Calcula si esta lavada (la siguiente) es gratis.
        $nuevoConteo = (int) $cliente['lavadas'] + 1;
        $esGratis    = ($nuevoConteo % LAVADAS_PARA_GRATIS) === 0;

        $clienteModel->registrarLavadaContador((int) $cliente['id'], $esGratis);

        (new Lavada())->crear([
            'cliente_id' => (int) $cliente['id'],
            'telefono'   => $tel,
            'nombre'     => $nombre,
            'placa'      => $placa ?: $cliente['placa'],
            'moto'       => $moto ?: $cliente['moto'],
            'precio'     => $esGratis ? 0 : $precio,
            'gratis'     => $esGratis,
            'num_lavada' => $nuevoConteo,
        ]);

        if ($esGratis) {
            $this->flash("🎉 ¡Lavada #{$nuevoConteo} GRATIS para {$nombre}!");
        } else {
            $faltan = LAVADAS_PARA_GRATIS - ($nuevoConteo % LAVADAS_PARA_GRATIS);
            $this->flash("✓ Lavada registrada. Faltan {$faltan} para la gratis");
        }
        $this->redirect('/');
    }

    /** Alterna el estado de pago de una lavada (pagada <-> debe). */
    public function pago(): void
    {
        $id = (int) $this->input('id', '0');
        if ($id > 0) {
            (new Lavada())->alternarPago($id);
        }
        $destino = $this->input('volver') === 'historial' ? '/historial' : '/';
        $this->redirect($destino);
    }

    /** Cambia el valor de una lavada del día. */
    public function precio(): void
    {
        $id     = (int) $this->input('id', '0');
        $precio = (int) preg_replace('/\D/', '', $this->input('precio', '0'));
        if ($id > 0 && $precio > 0) {
            (new Lavada())->actualizarPrecio($id, $precio);
            $this->flash('Valor actualizado');
        }
        $this->redirect('/');
    }

    /** Guarda la foto subida y devuelve el nombre del archivo (o null). */
    private function subirFoto(): ?string
    {
        if (empty($_FILES['foto']['tmp_name']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $info = getimagesize($_FILES['foto']['tmp_name']);
        if ($info === false) {
            return null; // no es una imagen válida
        }
        $ext = image_type_to_extension($info[2], false) ?: 'jpg';
        $nombre = 'moto_' . bin2hex(random_bytes(8)) . '.' . $ext;

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0775, true);
        }
        move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_PATH . '/' . $nombre);
        return $nombre;
    }
}
