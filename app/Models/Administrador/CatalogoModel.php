<?php

declare(strict_types=1);

namespace App\Models\Administrador;

use App\Database;
use InvalidArgumentException;

/**
 * Catálogos simples con nombre, descripción y borrado lógico (especialidades, servicios).
 */
final class CatalogoModel
{
    private const TABLES = ['especialidades', 'servicios'];

    public function __construct(private readonly string $table)
    {
        if (!in_array($table, self::TABLES, true)) {
            throw new InvalidArgumentException('Catálogo no permitido.');
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(string $search = ''): array
    {
        $sql = 'SELECT c.id, c.nombre, c.descripcion, c.activo' . $this->usageColumn() . ' FROM ' . $this->table . ' c';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE c.nombre LIKE :q';
            $params['q'] = '%' . $search . '%';
        }

        $statement = Database::connection()->prepare($sql . ' ORDER BY c.activo DESC, c.nombre');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function active(): array
    {
        return Database::connection()
            ->query('SELECT id, nombre FROM ' . $this->table . ' WHERE activo = 1 ORDER BY nombre')
            ->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, nombre, descripcion, activo FROM ' . $this->table . ' WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    public function isActive(int $id): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM ' . $this->table . ' WHERE id = :id AND activo = 1'
        );
        $statement->execute(['id' => $id]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function nameExists(string $name, int $exceptId = 0): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM ' . $this->table . ' WHERE nombre = :nombre AND id <> :id'
        );
        $statement->execute(['nombre' => $name, 'id' => $exceptId]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO ' . $this->table . ' (nombre, descripcion, activo) VALUES (:nombre, :descripcion, 1)'
        );
        $statement->execute(['nombre' => $data['nombre'], 'descripcion' => $data['descripcion'] ?: null]);

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE ' . $this->table . ' SET nombre = :nombre, descripcion = :descripcion WHERE id = :id'
        );
        $statement->execute(['nombre' => $data['nombre'], 'descripcion' => $data['descripcion'] ?: null, 'id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = Database::connection()->prepare('UPDATE ' . $this->table . ' SET activo = :activo WHERE id = :id');
        $statement->execute(['activo' => $active ? 1 : 0, 'id' => $id]);
    }

    private function usageColumn(): string
    {
        return $this->table === 'especialidades'
            ? ', (SELECT COUNT(*) FROM medicos m WHERE m.especialidad_id = c.id AND m.activo = 1) AS en_uso'
            : ', NULL AS en_uso';
    }
}
