<?php

declare(strict_types=1);

namespace App\Models\Citas;

use App\Database;
use DateTimeImmutable;
use PDO;
use Throwable;

/**
 * RF05: gestión de citas con verificación de disponibilidad (RF05.4) y sin empalmes (RNF04.1).
 */
final class CitaModel
{
    public const PER_PAGE = 50;

    /** Estados que ocupan el horario del médico (Apéndice B: «Cancelada» libera el horario). */
    private const OCUPAN = "('pendiente','confirmada','completada','no_asistio')";

    /** Transiciones permitidas (Apéndice B). */
    public const TRANSICIONES = [
        'pendiente' => ['confirmada', 'cancelada'],
        'confirmada' => ['completada', 'cancelada'],
        'completada' => [],
        'cancelada' => [],
        'no_asistio' => [],
        'vencida' => [],
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'completada' => 'Completada',
        'cancelada' => 'Cancelada',
        'no_asistio' => 'No asistió',
        'vencida' => 'Vencida',
    ];

    private const SELECT = "SELECT c.*,
            CONCAT(p.nombre, ' ', p.apellidos) AS paciente, p.telefono AS paciente_telefono, p.activo AS paciente_activo,
            m.nombre AS medico, e.nombre AS especialidad, s.nombre AS servicio,
            uc.nombre AS creado_por_nombre, ux.nombre AS cancelada_por_nombre
        FROM citas c
        INNER JOIN pacientes p ON p.id = c.paciente_id
        INNER JOIN medicos m ON m.id = c.medico_id
        INNER JOIN especialidades e ON e.id = m.especialidad_id
        LEFT JOIN servicios s ON s.id = c.servicio_id
        LEFT JOIN usuarios uc ON uc.id = c.creado_por
        LEFT JOIN usuarios ux ON ux.id = c.cancelada_por";

    /**
     * @return array{rows: array, total: int}
     */
    public function search(array $filters, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($filters['desde'] !== '') {
            $where[] = 'c.inicio >= :desde';
            $params['desde'] = $filters['desde'] . ' 00:00:00';
        }

        if ($filters['hasta'] !== '') {
            $where[] = 'c.inicio <= :hasta';
            $params['hasta'] = $filters['hasta'] . ' 23:59:59';
        }

        if ($filters['medico_id'] > 0) {
            $where[] = 'c.medico_id = :medico';
            $params['medico'] = $filters['medico_id'];
        }

        if (isset(self::ESTADOS[$filters['estado']])) {
            $where[] = 'c.estado = :estado';
            $params['estado'] = $filters['estado'];
        }

        if ($filters['q'] !== '') {
            $where[] = "(CONCAT(p.nombre, ' ', p.apellidos) LIKE :q1 OR p.telefono LIKE :q2)";
            $params['q1'] = $params['q2'] = '%' . $filters['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $db = Database::connection();

        $count = $db->prepare('SELECT COUNT(*) FROM citas c INNER JOIN pacientes p ON p.id = c.paciente_id WHERE ' . $whereSql);
        $count->execute($params);

        $statement = $db->prepare(
            self::SELECT . ' WHERE ' . $whereSql . ' ORDER BY c.inicio, m.nombre
             LIMIT ' . self::PER_PAGE . ' OFFSET ' . max(0, ($page - 1) * self::PER_PAGE)
        );
        $statement->execute($params);

        return ['rows' => $statement->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(self::SELECT . ' WHERE c.id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $row;
    }

    /**
     * RF03.3: citas de un paciente, de la más reciente a la más antigua.
     */
    public function forPaciente(int $pacienteId): array
    {
        $statement = Database::connection()->prepare(self::SELECT . ' WHERE c.paciente_id = :paciente ORDER BY c.inicio DESC');
        $statement->execute(['paciente' => $pacienteId]);

        return $statement->fetchAll();
    }

    public function pacientesActivos(): array
    {
        return Database::connection()->query(
            "SELECT id, CONCAT(apellidos, ', ', nombre) AS nombre, telefono FROM pacientes WHERE activo = 1 ORDER BY apellidos, nombre"
        )->fetchAll();
    }

    public function isActive(string $table, int $id): bool
    {
        if (!in_array($table, ['pacientes', 'medicos', 'servicios'], true)) {
            return false;
        }

        $statement = Database::connection()->prepare('SELECT COUNT(*) FROM ' . $table . ' WHERE id = :id AND activo = 1');
        $statement->execute(['id' => $id]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * RF05.4: horarios libres del médico para una fecha y duración.
     *
     * @return list<string> horas de inicio en formato HH:MM
     */
    public function slots(int $medicoId, string $fecha, int $duracion, int $exceptCitaId = 0): array
    {
        $day = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        if ($day === false || $duracion <= 0) {
            return [];
        }

        $now = new DateTimeImmutable();
        $taken = $this->busy($medicoId, $fecha, $exceptCitaId);
        $slots = [];

        foreach ($this->blocks($medicoId, (int) $day->format('N')) as $block) {
            $start = new DateTimeImmutable($fecha . ' ' . $block['hora_inicio']);
            $end = new DateTimeImmutable($fecha . ' ' . $block['hora_fin']);

            for ($slot = $start; $slot->modify('+' . $duracion . ' minutes') <= $end; $slot = $slot->modify('+' . $duracion . ' minutes')) {
                $slotEnd = $slot->modify('+' . $duracion . ' minutes');

                if ($slot <= $now) {
                    continue;
                }

                foreach ($taken as [$busyStart, $busyEnd]) {
                    if ($slot < $busyEnd && $slotEnd > $busyStart) {
                        continue 2;
                    }
                }

                $slots[] = $slot->format('H:i');
            }
        }

        return $slots;
    }

    /**
     * Comprueba que [inicio, fin) cae completo dentro de un bloque de horario del médico.
     */
    public function withinSchedule(int $medicoId, string $inicio, string $fin): bool
    {
        $start = new DateTimeImmutable($inicio);
        $end = new DateTimeImmutable($fin);

        if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
            return false;
        }

        foreach ($this->blocks($medicoId, (int) $start->format('N')) as $block) {
            if ($start->format('H:i:s') >= $block['hora_inicio'] && $end->format('H:i:s') <= $block['hora_fin']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea o reprograma una cita dentro de una transacción que bloquea al médico y al paciente
     * (SELECT … FOR UPDATE). Así dos recepcionistas no pueden reservar el mismo horario a la vez
     * (RNF04.1). Devuelve el id de la cita o un mensaje de error.
     *
     * @return array{ok: bool, id?: int, error?: string}
     */
    public function save(array $data, int $citaId = 0): array
    {
        $db = Database::connection();
        $db->beginTransaction();

        try {
            $lock = $db->prepare('SELECT id FROM medicos WHERE id = :id AND activo = 1 FOR UPDATE');
            $lock->execute(['id' => $data['medico_id']]);

            if ($lock->fetchColumn() === false) {
                $db->rollBack();
                return ['ok' => false, 'error' => 'El médico no está disponible.'];
            }

            $db->prepare('SELECT id FROM pacientes WHERE id = :id FOR UPDATE')->execute(['id' => $data['paciente_id']]);

            if ($this->overlaps('medico_id', $data['medico_id'], $data['inicio'], $data['fin'], $citaId)) {
                $db->rollBack();
                return ['ok' => false, 'error' => 'El médico ya tiene una cita en ese horario. Elige otro.'];
            }

            if ($this->overlaps('paciente_id', $data['paciente_id'], $data['inicio'], $data['fin'], $citaId)) {
                $db->rollBack();
                return ['ok' => false, 'error' => 'El paciente ya tiene otra cita que se empalma con ese horario.'];
            }

            if ($citaId === 0) {
                $statement = $db->prepare(
                    'INSERT INTO citas (paciente_id, medico_id, servicio_id, inicio, fin, estado, motivo, observaciones, creado_por)
                     VALUES (:paciente_id, :medico_id, :servicio_id, :inicio, :fin, :estado, :motivo, :observaciones, :creado_por)'
                );
                $statement->execute([
                    'paciente_id' => $data['paciente_id'],
                    'medico_id' => $data['medico_id'],
                    'servicio_id' => $data['servicio_id'],
                    'inicio' => $data['inicio'],
                    'fin' => $data['fin'],
                    'estado' => $data['estado'] ?? 'pendiente',
                    'motivo' => $data['motivo'] ?: null,
                    'observaciones' => $data['observaciones'] ?: null,
                    'creado_por' => $data['creado_por'],
                ]);
                $citaId = (int) $db->lastInsertId();
            } else {
                $statement = $db->prepare(
                    "UPDATE citas SET paciente_id = :paciente_id, medico_id = :medico_id, servicio_id = :servicio_id,
                        inicio = :inicio, fin = :fin, motivo = :motivo, observaciones = :observaciones
                     WHERE id = :id AND estado IN ('pendiente', 'confirmada')"
                );
                $statement->execute([
                    'paciente_id' => $data['paciente_id'],
                    'medico_id' => $data['medico_id'],
                    'servicio_id' => $data['servicio_id'],
                    'inicio' => $data['inicio'],
                    'fin' => $data['fin'],
                    'motivo' => $data['motivo'] ?: null,
                    'observaciones' => $data['observaciones'] ?: null,
                    'id' => $citaId,
                ]);
            }

            $db->commit();

            return ['ok' => true, 'id' => $citaId];
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Aplica una transición de estado validada contra el Apéndice B.
     */
    public function changeStatus(int $id, string $from, string $to, int $userId, ?string $motivo = null): bool
    {
        if (!in_array($to, self::TRANSICIONES[$from] ?? [], true)) {
            return false;
        }

        $sql = 'UPDATE citas SET estado = :to';
        $params = ['to' => $to, 'id' => $id, 'from' => $from];

        if ($to === 'cancelada') {
            $sql .= ', cancelada_por = :user, motivo_cancelacion = :motivo';
            $params['user'] = $userId;
            $params['motivo'] = $motivo !== null && $motivo !== '' ? mb_substr($motivo, 0, 255) : null;
        }

        // La condición sobre el estado actual evita aplicar dos veces la misma transición.
        $statement = Database::connection()->prepare($sql . ' WHERE id = :id AND estado = :from');
        $statement->execute($params);

        return $statement->rowCount() === 1;
    }

    private function overlaps(string $column, int $id, string $inicio, string $fin, int $exceptCitaId): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM citas
             WHERE ' . $column . ' = :id AND estado IN ' . self::OCUPAN . '
               AND inicio < :fin AND fin > :inicio AND id <> :except'
        );
        $statement->execute(['id' => $id, 'inicio' => $inicio, 'fin' => $fin, 'except' => $exceptCitaId]);

        return (int) $statement->fetchColumn() > 0;
    }

    private function blocks(int $medicoId, int $weekday): array
    {
        $statement = Database::connection()->prepare(
            'SELECT hora_inicio, hora_fin FROM horarios_medico
             WHERE medico_id = :medico AND dia_semana = :dia AND activo = 1
             ORDER BY hora_inicio'
        );
        $statement->execute(['medico' => $medicoId, 'dia' => $weekday]);

        return $statement->fetchAll();
    }

    /**
     * @return list<array{0: DateTimeImmutable, 1: DateTimeImmutable}>
     */
    private function busy(int $medicoId, string $fecha, int $exceptCitaId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT inicio, fin FROM citas
             WHERE medico_id = :medico AND estado IN ' . self::OCUPAN . '
               AND inicio < :hasta AND fin > :desde AND id <> :except'
        );
        $statement->execute([
            'medico' => $medicoId,
            'desde' => $fecha . ' 00:00:00',
            'hasta' => $fecha . ' 23:59:59',
            'except' => $exceptCitaId,
        ]);

        return array_map(
            static fn (array $row): array => [new DateTimeImmutable($row['inicio']), new DateTimeImmutable($row['fin'])],
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
