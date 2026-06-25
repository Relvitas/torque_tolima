<?php
namespace App\Models;

use App\Core\Model;

class Egreso extends Model
{
    /** Inserta un egreso y devuelve su id. */
    public function crear(array $d): int
    {
        $this->run(
            'INSERT INTO egresos (concepto, categoria, monto, nota)
             VALUES (?, ?, ?, ?)',
            [$d['concepto'], $d['categoria'], $d['monto'], $d['nota'] ?: null]
        );
        return (int) $this->db->lastInsertId();
    }

    /** Lista de egresos, opcionalmente filtrados por mes 'YYYY-MM'. */
    public function todos(string $mes = ''): array
    {
        if ($mes === '') {
            return $this->all('SELECT * FROM egresos ORDER BY creado_en DESC');
        }
        return $this->all(
            "SELECT * FROM egresos
             WHERE DATE_FORMAT(creado_en, '%Y-%m') = ?
             ORDER BY creado_en DESC",
            [$mes]
        );
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM egresos WHERE id = ?', [$id]);
    }

    /** Total de egresos del mes actual. */
    public function totalMes(string $mes): int
    {
        return (int) ($this->one(
            "SELECT COALESCE(SUM(monto),0) s FROM egresos
             WHERE DATE_FORMAT(creado_en, '%Y-%m') = ?",
            [$mes]
        )['s'] ?? 0);
    }

    /** Total de egresos de hoy. */
    public function totalHoy(): int
    {
        return (int) ($this->one(
            'SELECT COALESCE(SUM(monto),0) s FROM egresos WHERE DATE(creado_en) = CURDATE()'
        )['s'] ?? 0);
    }

    /** Egresos agrupados por mes (más reciente primero). */
    public function porMes(): array
    {
        return $this->all(
            "SELECT DATE_FORMAT(creado_en, '%Y-%m') AS mes,
                    COUNT(*)                         AS cantidad,
                    COALESCE(SUM(monto), 0)          AS total
             FROM egresos
             GROUP BY mes
             ORDER BY mes DESC"
        );
    }
}
