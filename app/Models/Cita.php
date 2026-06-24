<?php
namespace App\Models;

use App\Core\Model;

class Cita extends Model
{
    /** Todas las citas (orden cronológico). */
    public function todas(): array
    {
        return $this->all('SELECT * FROM citas ORDER BY fecha ASC, hora ASC');
    }

    /** Citas de una fecha concreta (Y-m-d). */
    public function porFecha(string $fecha): array
    {
        return $this->all(
            'SELECT * FROM citas WHERE fecha = ? ORDER BY hora ASC',
            [$fecha]
        );
    }

    /** Próximas citas a partir de hoy. */
    public function proximas(int $limite = 8): array
    {
        return $this->all(
            'SELECT * FROM citas
             WHERE fecha >= CURDATE()
             ORDER BY fecha ASC, hora ASC
             LIMIT ' . (int) $limite
        );
    }

    /** ¿Está ocupada esa fecha+hora? */
    public function estaOcupada(string $fecha, string $hora): bool
    {
        return $this->one(
            'SELECT id FROM citas WHERE fecha = ? AND hora = ?',
            [$fecha, $hora]
        ) !== null;
    }

    public function crear(array $d): bool
    {
        if ($this->estaOcupada($d['fecha'], $d['hora'])) {
            return false;
        }
        $this->run(
            'INSERT INTO citas (fecha, hora, nombre, telefono, placa, nota)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$d['fecha'], $d['hora'], $d['nombre'], $d['telefono'], $d['placa'], $d['nota']]
        );
        return true;
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM citas WHERE id = ?', [$id]);
    }
}
