<?php
namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    /** Busca un cliente por teléfono. */
    public function porTelefono(string $tel): ?array
    {
        return $this->one('SELECT * FROM clientes WHERE telefono = ?', [$tel]);
    }

    public function porId(int $id): ?array
    {
        return $this->one('SELECT * FROM clientes WHERE id = ?', [$id]);
    }

    /** Lista clientes con filtro opcional por nombre, teléfono o placa. */
    public function buscar(string $q = ''): array
    {
        if ($q === '') {
            return $this->all('SELECT * FROM clientes ORDER BY nombre ASC');
        }
        $like = '%' . $q . '%';
        return $this->all(
            'SELECT * FROM clientes
             WHERE nombre LIKE ? OR telefono LIKE ? OR placa LIKE ?
             ORDER BY nombre ASC',
            [$like, $like, $like]
        );
    }

    /**
     * Crea o actualiza un cliente y devuelve su fila actualizada.
     * No incrementa el contador de lavadas (eso lo hace registrarLavada).
     */
    public function guardarDatos(string $tel, string $nombre, ?string $placa, ?string $moto, ?string $foto): array
    {
        $existente = $this->porTelefono($tel);

        if ($existente === null) {
            $this->run(
                'INSERT INTO clientes (telefono, nombre, placa, moto, foto)
                 VALUES (?, ?, ?, ?, ?)',
                [$tel, $nombre, $placa, $moto, $foto]
            );
        } else {
            // Solo sobrescribe placa/moto/foto si llegan valores nuevos.
            $this->run(
                'UPDATE clientes SET
                    nombre = ?,
                    placa  = COALESCE(NULLIF(?, ""), placa),
                    moto   = COALESCE(NULLIF(?, ""), moto),
                    foto   = COALESCE(?, foto)
                 WHERE telefono = ?',
                [$nombre, $placa ?? '', $moto ?? '', $foto, $tel]
            );
        }
        return $this->porTelefono($tel);
    }

    /**
     * Actualiza los datos editables de un cliente y sincroniza los snapshots
     * del historial de lavadas para que todo quede coherente.
     * La foto solo se reemplaza si llega una nueva ($foto !== null).
     */
    public function actualizar(int $id, array $d, ?string $foto = null): void
    {
        if ($foto !== null) {
            $this->run(
                'UPDATE clientes SET nombre = ?, telefono = ?, placa = ?, moto = ?, foto = ? WHERE id = ?',
                [$d['nombre'], $d['telefono'], $d['placa'], $d['moto'], $foto, $id]
            );
        } else {
            $this->run(
                'UPDATE clientes SET nombre = ?, telefono = ?, placa = ?, moto = ? WHERE id = ?',
                [$d['nombre'], $d['telefono'], $d['placa'], $d['moto'], $id]
            );
        }
        // Mantiene coherente el historial (telefono/nombre/placa/moto son snapshots).
        $this->run(
            'UPDATE lavadas SET telefono = ?, nombre = ?, placa = ?, moto = ? WHERE cliente_id = ?',
            [$d['telefono'], $d['nombre'], $d['placa'], $d['moto'], $id]
        );
    }

    /** Incrementa el contador de lavadas (y de gratis si aplica). */
    public function registrarLavadaContador(int $id, bool $gratis): void
    {
        $this->run(
            'UPDATE clientes
             SET lavadas = lavadas + 1,
                 total_gratis = total_gratis + ?
             WHERE id = ?',
            [$gratis ? 1 : 0, $id]
        );
    }

    public function contar(): int
    {
        return (int) ($this->one('SELECT COUNT(*) c FROM clientes')['c'] ?? 0);
    }

    /**
     * Elimina un cliente y todas sus lavadas (FK ON DELETE CASCADE).
     * También borra su foto del disco si existe.
     */
    public function eliminar(int $id): void
    {
        $cliente = $this->porId($id);
        if ($cliente === null) {
            return;
        }
        if (!empty($cliente['foto'])) {
            $ruta = UPLOAD_PATH . '/' . basename($cliente['foto']);
            if (is_file($ruta)) {
                @unlink($ruta);
            }
        }
        $this->run('DELETE FROM clientes WHERE id = ?', [$id]);
    }

    /** Top N clientes por número de lavadas. */
    public function top(int $limite = 6): array
    {
        return $this->all(
            'SELECT * FROM clientes ORDER BY lavadas DESC LIMIT ' . (int) $limite
        );
    }
}
