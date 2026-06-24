<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cita;

class CitaController extends Controller
{
    /** Horarios disponibles del negocio. */
    public const HORARIOS = [
        '8:00','8:30','9:00','9:30','10:00','10:30','11:00','11:30',
        '12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30',
        '16:00','16:30','17:00','17:30',
    ];

    public function index(): void
    {
        $model = new Cita();
        $this->view('citas/index', [
            'seccion'  => 'citas',
            'citas'    => $model->todas(),
            'proximas' => $model->proximas(),
            'horarios' => self::HORARIOS,
            'waNum'    => WA_NUM,
        ]);
    }

    public function agendar(): void
    {
        $fecha  = $this->input('fecha');
        $hora   = $this->input('hora');
        $nombre = $this->input('nombre');
        $tel    = $this->input('telefono');

        if ($nombre === '' || $tel === '') {
            $this->flash('Ingresa nombre y teléfono');
            $this->redirect('/citas');
        }
        if ($fecha === '' || $hora === '') {
            $this->flash('Selecciona fecha y hora');
            $this->redirect('/citas');
        }

        $ok = (new Cita())->crear([
            'fecha'    => $fecha,
            'hora'     => $hora,
            'nombre'   => $nombre,
            'telefono' => $tel,
            'placa'    => strtoupper($this->input('placa')),
            'nota'     => $this->input('nota'),
        ]);

        $this->flash($ok ? "Cita agendada para las {$hora}" : 'Ese horario ya está ocupado');
        $this->redirect('/citas');
    }

    public function eliminar(): void
    {
        $id = (int) $this->input('id', '0');
        if ($id > 0) {
            (new Cita())->eliminar($id);
            $this->flash('Cita eliminada');
        }
        $this->redirect('/citas');
    }
}
