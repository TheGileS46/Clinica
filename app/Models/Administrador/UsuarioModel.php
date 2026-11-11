<?php

declare(strict_types=1);

namespace App\Models\Administrador;

use App\Database;

final class UsuarioModel
{
    /**
     * @return list<array<string, mixed>>
     */
    public function all(string $search = '', string $estado = ''): array
    {
        $sql = 'SELECT u.id, u.nombre, u.correo, u.activo, u.rol_id, u.created_at, r.nombre AS rol, m.id AS medico_id
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                LEFT JOIN medicos m ON m.usuario_id = u.id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (u.nombre LIKE :q1 OR u.correo LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $search . '%';
        }

        if ($estado === 'activos' || $estado === 'inactivos') {
            $sql .= ' AND u.activo = :activo';
            $params['activo'] = $estado === 'activos' ? 1 : 0;
        }

        $statement = Database::connection()->prepare($sql . ' ORDER BY u.activo DESC, u.nombre');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT u.id, u.nombre, u.correo, u.activo, u.rol_id, r.nombre AS rol
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function roles(): array
    {
        return Database::connection()->query('SELECT id, nombre FROM roles ORDER BY id')->fetchAll();
    }

    public function roleName(int $roleId): ?string
    {
        $statement = Database::connection()->prepare('SELECT nombre FROM roles WHERE id = :id');
        $statement->execute(['id' => $roleId]);
        $name = $statement->fetchColumn();

        return $name === false ? null : (string) $name;
    }

    public function emailExists(string $email, int $exceptId = 0): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id <> :id'
        );
        $statement->execute(['correo' => $email, 'id' => $exceptId]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO usuarios (nombre, correo, password, rol_id, activo)
             VALUES (:nombre, :correo, :password, :rol_id, :activo)'
        );
        $statement->execute([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'rol_id' => $data['rol_id'],
            'activo' => $data['activo'] ? 1 : 0,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $fields = 'nombre = :nombre, correo = :correo, rol_id = :rol_id';
        $params = [
            'id' => $id,
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'rol_id' => $data['rol_id'],
        ];

        if ($data['password'] !== '') {
            $fields .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $statement = Database::connection()->prepare('UPDATE usuarios SET ' . $fields . ' WHERE id = :id');
        $statement->execute($params);
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = Database::connection()->prepare('UPDATE usuarios SET activo = :activo WHERE id = :id');
        $statement->execute(['activo' => $active ? 1 : 0, 'id' => $id]);
    }
}
