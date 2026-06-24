<?php
namespace App\Core;

use PDO;

/**
 * Modelo base: expone la conexión PDO y helpers de consulta.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** Ejecuta una consulta preparada y devuelve el statement. */
    protected function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Devuelve todas las filas. */
    protected function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** Devuelve una sola fila o null. */
    protected function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }
}
