<?php

declare(strict_types=1);

namespace App\Models\Pacientes;

use App\Database;

/**
 * RF03: registro, consulta y modificación de pacientes con desactivación lógica.
 */
final class PacienteModel
{
    public const PER_PAGE = 25;

    /**
     * @return array{rows: array, total: int}
     */
    public function search(string $search, string $estado, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($search !== '') {
            $where[] = "(CONCAT(p.nombre, ' ', p.apellidos) LIKE :q1 OR p.telefono LIKE :q2 OR p.correo LIKE :q3 OR p.curp_dni LIKE :q4)";
            $params['q1'] = $params['q2'] = $params['q3'] = $params['q4'] = '%' . $search . '%';
        }

        if ($estado !== 'todos') {
            $where[] = 'p.activo = :activo';
            $params['activo'] = $estado === 'inactivos' ? 0 : 1;
        }

        $whereSql = implode(' AND ', $where);
        $db = Database::connection();

        $count = $db->prepare('SELECT COUNT(*) FROM pacientes p WHERE ' . $whereSql);
        $count->execute($params);

        $statement = $db->prepare(
            'SELECT p.id, p.nombre, p.apellidos, p.telefono, p.correo, p.fecha_nacimiento, p.curp_dni, p.activo
             FROM pacientes p WHERE ' . $whereSql . '
             ORDER BY p.apellidos, p.nombre
             LIMIT ' . self::PER_PAGE . ' OFFSET ' . max(0, ($page - 1) * self::PER_PAGE)
        );
        $statement->execute($params);

        return ['rows' => $statement->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM pacientes WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public function curpExists(string $curp, int $exceptId = 0): bool
    {
        $statement = Database::connection()->prepare('SELECT COUNT(*) FROM pacientes WHERE curp_dni = :curp AND id <> :id');
        $statement->execute(['curp' => $curp, 'id' => $exceptId]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO pacientes (nombre, apellidos, fecha_nacimiento, curp_dni, telefono, correo, direccion, activo)
             VALUES (:nombre, :apellidos, :fecha_nacimiento, :curp_dni, :telefono, :correo, :direccion, 1)'
        );
        $statement->execute($this->params($data));

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE pacientes SET nombre = :nombre, apellidos = :apellidos, fecha_nacimiento = :fecha_nacimiento,
                curp_dni = :curp_dni, telefono = :telefono, correo = :correo, direccion = :direccion
             WHERE id = :id'
        );
        $statement->execute($this->params($data) + ['id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = Database::connection()->prepare('UPDATE pacientes SET activo = :activo WHERE id = :id');
        $statement->execute(['activo' => $active ? 1 : 0, 'id' => $id]);
    }

    private function params(array $data): array
    {
        return [
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            'curp_dni' => $data['curp_dni'] ?: null,
            'telefono' => $data['telefono'],
            'correo' => $data['correo'] ?: null,
            'direccion' => $data['direccion'] ?: null,
        ];
    }
}
