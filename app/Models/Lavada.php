<?php
namespace App\Models;

use App\Core\Model;

class Lavada extends Model
{
    /** Inserta una lavada en el historial y devuelve su id. */
    public function crear(array $d): int
    {
        $this->run(
            'INSERT INTO lavadas
                (cliente_id, telefono, nombre, placa, moto, precio, gratis, num_lavada)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $d['cliente_id'], $d['telefono'], $d['nombre'],
                $d['placa'], $d['moto'], $d['precio'],
                $d['gratis'] ? 1 : 0, $d['num_lavada'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function porId(int $id): ?array
    {
        return $this->one('SELECT * FROM lavadas WHERE id = ?', [$id]);
    }

    /** Historial filtrable por nombre, teléfono o placa. */
    public function historial(string $q = ''): array
    {
        if ($q === '') {
            return $this->all('SELECT * FROM lavadas ORDER BY creado_en DESC');
        }
        $like = '%' . $q . '%';
        return $this->all(
            'SELECT * FROM lavadas
             WHERE nombre LIKE ? OR telefono LIKE ? OR placa LIKE ?
             ORDER BY creado_en DESC',
            [$like, $like, $like]
        );
    }

    public function contar(): int
    {
        return (int) ($this->one('SELECT COUNT(*) c FROM lavadas')['c'] ?? 0);
    }

    /** Cambia el valor (precio) de una lavada. No aplica a las gratis. */
    public function actualizarPrecio(int $id, int $precio): void
    {
        $this->run(
            'UPDATE lavadas SET precio = ? WHERE id = ? AND gratis = 0',
            [max(0, $precio), $id]
        );
    }

    /** Alterna el estado de pago de una lavada (pagada <-> pendiente). Las gratis no cambian. */
    public function alternarPago(int $id): void
    {
        $this->run('UPDATE lavadas SET pagado = 1 - pagado WHERE id = ? AND gratis = 0', [$id]);
    }

    /** Lavadas registradas hoy (más reciente primero). */
    public function deHoy(): array
    {
        return $this->all(
            'SELECT * FROM lavadas WHERE DATE(creado_en) = CURDATE() ORDER BY creado_en DESC'
        );
    }

    /** Lavadas registradas hoy. */
    public function contarHoy(): int
    {
        return (int) ($this->one(
            'SELECT COUNT(*) c FROM lavadas WHERE DATE(creado_en) = CURDATE()'
        )['c'] ?? 0);
    }

    /**
     * Elimina una lavada del historial y reajusta los contadores del cliente
     * (lavadas y, si era gratis, total_gratis) para mantener la consistencia.
     */
    public function eliminar(int $id): void
    {
        $lavada = $this->porId($id);
        if ($lavada === null) {
            return;
        }
        $this->run('DELETE FROM lavadas WHERE id = ?', [$id]);
        $this->run(
            'UPDATE clientes
             SET lavadas = GREATEST(lavadas - 1, 0),
                 total_gratis = GREATEST(total_gratis - ?, 0)
             WHERE id = ?',
            [$lavada['gratis'] ? 1 : 0, (int) $lavada['cliente_id']]
        );
    }

    public function ingresosTotales(): int
    {
        return (int) ($this->one(
            'SELECT COALESCE(SUM(precio),0) s FROM lavadas WHERE gratis = 0'
        )['s'] ?? 0);
    }

    /** Ingresos generados hoy. */
    public function ingresosHoy(): int
    {
        return (int) ($this->one(
            'SELECT COALESCE(SUM(precio),0) s FROM lavadas
             WHERE gratis = 0 AND DATE(creado_en) = CURDATE()'
        )['s'] ?? 0);
    }

    public function totalGratis(): int
    {
        return (int) ($this->one(
            'SELECT COUNT(*) c FROM lavadas WHERE gratis = 1'
        )['c'] ?? 0);
    }

    /** Total generado, cantidad y gratis agrupado por mes (más reciente primero). */
    public function ingresosPorMes(): array
    {
        return $this->all(
            "SELECT DATE_FORMAT(creado_en, '%Y-%m') AS mes,
                    COUNT(*)                         AS cantidad,
                    SUM(gratis)                      AS gratis,
                    COALESCE(SUM(precio), 0)         AS total
             FROM lavadas
             GROUP BY mes
             ORDER BY mes DESC"
        );
    }

    /** Conteo de lavadas pagadas agrupadas por precio. */
    public function ingresosPorTipo(): array
    {
        return $this->all(
            'SELECT precio, COUNT(*) cantidad
             FROM lavadas
             WHERE gratis = 0
             GROUP BY precio
             ORDER BY cantidad DESC'
        );
    }
}
