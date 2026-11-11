<?php

declare(strict_types=1);

namespace App\Models\Administrador;

use App\Database;

/**
 * RF04: registro y consulta de médicos, su especialidad, cuenta vinculada y horarios.
 */
final class MedicoModel
{
    public const DIAS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    public function all(string $search = '', int $especialidadId = 0, string $estado = ''): array
    {
        $sql = 'SELECT m.id, m.nombre, m.cedula_profesional, m.telefono, m.correo, m.activo, m.usuario_id,
                       e.nombre AS especialidad, u.correo AS usuario_correo, u.activo AS usuario_activo,
                       (SELECT COUNT(*) FROM horarios_medico h WHERE h.medico_id = m.id AND h.activo = 1) AS horarios
                FROM medicos m
                INNER JOIN especialidades e ON e.id = m.especialidad_id
                LEFT JOIN usuarios u ON u.id = m.usuario_id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (m.nombre LIKE :q1 OR m.cedula_profesional LIKE :q2 OR e.nombre LIKE :q3)';
            $params['q1'] = $params['q2'] = $params['q3'] = '%' . $search . '%';
        }

        if ($especialidadId > 0) {
            $sql .= ' AND m.especialidad_id = :especialidad';
            $params['especialidad'] = $especialidadId;
        }

        if ($estado === 'activos' || $estado === 'inactivos') {
            $sql .= ' AND m.activo = :activo';
            $params['activo'] = $estado === 'activos' ? 1 : 0;
        }

        $statement = Database::connection()->prepare($sql . ' ORDER BY m.activo DESC, m.nombre');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function active(): array
    {
        return Database::connection()->query(
            'SELECT m.id, m.nombre, e.nombre AS especialidad
             FROM medicos m INNER JOIN especialidades e ON e.id = m.especialidad_id
             WHERE m.activo = 1 ORDER BY m.nombre'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT m.*, e.nombre AS especialidad
             FROM medicos m INNER JOIN especialidades e ON e.id = m.especialidad_id
             WHERE m.id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Cuentas con rol Médico que aún no están vinculadas a otro médico.
     */
    public function availableUsers(int $medicoId = 0): array
    {
        $statement = Database::connection()->prepare(
            "SELECT u.id, u.nombre, u.correo, u.activo
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             LEFT JOIN medicos m ON m.usuario_id = u.id AND m.id <> :medico
             WHERE r.nombre IN ('Médico', 'Medico') AND m.id IS NULL
             ORDER BY u.nombre"
        );
        $statement->execute(['medico' => $medicoId]);

        return $statement->fetchAll();
    }

    public function isAvailableUser(int $userId, int $medicoId): bool
    {
        foreach ($this->availableUsers($medicoId) as $user) {
            if ((int) $user['id'] === $userId) {
                return true;
            }
        }

        return false;
    }

    public function cedulaExists(string $cedula, int $exceptId = 0): bool
    {
        $statement = Database::connection()->prepare('SELECT COUNT(*) FROM medicos WHERE cedula_profesional = :cedula AND id <> :id');
        $statement->execute(['cedula' => $cedula, 'id' => $exceptId]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO medicos (usuario_id, especialidad_id, nombre, cedula_profesional, telefono, correo, activo)
             VALUES (:usuario_id, :especialidad_id, :nombre, :cedula, :telefono, :correo, 1)'
        );
        $statement->execute($this->params($data));

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE medicos SET usuario_id = :usuario_id, especialidad_id = :especialidad_id, nombre = :nombre,
                cedula_profesional = :cedula, telefono = :telefono, correo = :correo
             WHERE id = :id'
        );
        $statement->execute($this->params($data) + ['id' => $id]);
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = Database::connection()->prepare('UPDATE medicos SET activo = :activo WHERE id = :id');
        $statement->execute(['activo' => $active ? 1 : 0, 'id' => $id]);
    }

    public function horarios(int $medicoId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, dia_semana, hora_inicio, hora_fin
             FROM horarios_medico WHERE medico_id = :medico AND activo = 1
             ORDER BY dia_semana, hora_inicio'
        );
        $statement->execute(['medico' => $medicoId]);

        return $statement->fetchAll();
    }

    public function horarioOverlaps(int $medicoId, int $dia, string $inicio, string $fin): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM horarios_medico
             WHERE medico_id = :medico AND dia_semana = :dia AND activo = 1
               AND hora_inicio < :fin AND hora_fin > :inicio'
        );
        $statement->execute(['medico' => $medicoId, 'dia' => $dia, 'inicio' => $inicio, 'fin' => $fin]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function addHorario(int $medicoId, int $dia, string $inicio, string $fin): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO horarios_medico (medico_id, dia_semana, hora_inicio, hora_fin, activo) VALUES (:medico, :dia, :inicio, :fin, 1)'
        );
        $statement->execute(['medico' => $medicoId, 'dia' => $dia, 'inicio' => $inicio, 'fin' => $fin]);
    }

    public function removeHorario(int $medicoId, int $horarioId): bool
    {
        $statement = Database::connection()->prepare('UPDATE horarios_medico SET activo = 0 WHERE id = :id AND medico_id = :medico');
        $statement->execute(['id' => $horarioId, 'medico' => $medicoId]);

        return $statement->rowCount() > 0;
    }

    private function params(array $data): array
    {
        return [
            'usuario_id' => $data['usuario_id'] ?: null,
            'especialidad_id' => $data['especialidad_id'],
            'nombre' => $data['nombre'],
            'cedula' => $data['cedula_profesional'] ?: null,
            'telefono' => $data['telefono'] ?: null,
            'correo' => $data['correo'] ?: null,
        ];
    }
}
