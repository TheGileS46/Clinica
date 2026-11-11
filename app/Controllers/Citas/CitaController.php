<?php

declare(strict_types=1);

namespace App\Controllers\Citas;

use App\Controllers\Controller;
use App\Models\Administrador\CatalogoModel;
use App\Models\Administrador\MedicoModel;
use App\Models\Citas\CitaModel;
use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Flash;
use DateTimeImmutable;

/**
 * HU-10 · RF05: agendar, modificar, confirmar y cancelar citas verificando la disponibilidad.
 * Acceso: Recepción y Administrador (Apéndice C).
 */
final class CitaController extends Controller
{
    public const DURACIONES = [15, 20, 30, 45, 60, 90];

    private CitaModel $citas;

    private array $actor;

    public function __construct()
    {
        $this->actor = Auth::requireRole('Recepción', 'Administrador');
        $this->citas = new CitaModel();
    }

    public function index(): void
    {
        $desde = $this->validDate($this->query('desde')) ? $this->query('desde') : date('Y-m-d');
        $hasta = $this->validDate($this->query('hasta')) ? $this->query('hasta') : $desde;

        if ($hasta < $desde) {
            $hasta = $desde;
        }

        $filters = [
            'desde' => $desde,
            'hasta' => $hasta,
            'medico_id' => (int) $this->query('medico_id', '0'),
            'estado' => $this->query('estado'),
            'q' => $this->query('q'),
        ];
        $page = max(1, (int) $this->query('pagina', '1'));
        $result = $this->citas->search($filters, $page);

        $this->render('citas/index', [
            'pageTitle' => 'Citas',
            'filters' => $filters,
            'citas' => $result['rows'],
            'total' => $result['total'],
            'page' => $page,
            'pages' => max(1, (int) ceil($result['total'] / CitaModel::PER_PAGE)),
            'medicos' => (new MedicoModel())->all(),
            'estados' => CitaModel::ESTADOS,
        ]);
    }

    public function show(): void
    {
        $cita = $this->citas->find($this->idFrom($_GET)) ?? $this->notFound();

        $this->render('citas/show', [
            'pageTitle' => 'Cita #' . $cita['id'],
            'cita' => $cita,
            'estados' => CitaModel::ESTADOS,
            'transiciones' => CitaModel::TRANSICIONES[$cita['estado']] ?? [],
            'yaOcurrio' => new DateTimeImmutable($cita['inicio']) <= new DateTimeImmutable(),
        ]);
    }

    public function create(): void
    {
        $servicios = (new CatalogoModel('servicios'))->active();

        $this->form([
            'paciente_id' => (int) $this->query('paciente_id', '0'),
            'medico_id' => (int) $this->query('medico_id', '0'),
            'servicio_id' => (int) $this->query('servicio_id', (string) ($servicios[0]['id'] ?? 0)),
            'fecha' => $this->validDate($this->query('fecha')) ? $this->query('fecha') : date('Y-m-d'),
            'duracion' => in_array((int) $this->query('duracion'), self::DURACIONES, true) ? (int) $this->query('duracion') : 30,
            'hora' => '',
            'motivo' => $this->query('motivo'),
            'observaciones' => '',
        ]);
    }

    public function store(): void
    {
        $values = $this->values();
        $errors = $this->validate($values);

        if ($errors === []) {
            $result = $this->citas->save($this->payload($values));

            if ($result['ok']) {
                $cita = $this->citas->find($result['id']);
                Bitacora::registrar('cita_creada', 'exito', $this->actor['id'], $this->actor['correo'], $this->resumen($cita));
                Flash::set('success', 'Cita agendada para el ' . fecha($cita['inicio']) . ' con ' . $cita['medico'] . '.');
                $this->redirect('/citas/ver', ['id' => $result['id']]);
            }

            $errors['hora'] = $result['error'];
        }

        $this->form($values, $errors);
    }

    public function edit(): void
    {
        $cita = $this->editable($this->idFrom($_GET));
        $inicio = new DateTimeImmutable($cita['inicio']);
        $duracion = (int) round((strtotime($cita['fin']) - strtotime($cita['inicio'])) / 60);

        $this->form([
            'id' => (int) $cita['id'],
            'paciente_id' => (int) $cita['paciente_id'],
            'medico_id' => (int) $cita['medico_id'],
            'servicio_id' => (int) $cita['servicio_id'],
            'fecha' => $inicio->format('Y-m-d'),
            'duracion' => $duracion,
            'hora' => $inicio->format('H:i'),
            'motivo' => (string) $cita['motivo'],
            'observaciones' => (string) $cita['observaciones'],
        ], [], $cita);
    }

    public function update(): void
    {
        $cita = $this->editable($this->idFrom($_POST));
        $values = $this->values() + ['id' => (int) $cita['id']];
        $errors = $this->validate($values);

        if ($errors === []) {
            $result = $this->citas->save($this->payload($values), (int) $cita['id']);

            if ($result['ok']) {
                $nueva = $this->citas->find((int) $cita['id']);
                $detalle = $this->resumen($nueva);

                if ($nueva['inicio'] !== $cita['inicio'] || (int) $nueva['medico_id'] !== (int) $cita['medico_id']) {
                    $detalle .= ' · antes: ' . fecha($cita['inicio']) . ' con ' . $cita['medico'];
                }

                Bitacora::registrar('cita_modificada', 'exito', $this->actor['id'], $this->actor['correo'], $detalle);
                Flash::set('success', 'Cita actualizada.');
                $this->redirect('/citas/ver', ['id' => $cita['id']]);
            }

            $errors['hora'] = $result['error'];
        }

        $this->form($values, $errors, $cita);
    }

    /**
     * RF05.2 / Apéndice B: confirmar, completar o cancelar.
     */
    public function status(): void
    {
        $cita = $this->citas->find($this->idFrom($_POST)) ?? $this->notFound();
        $to = $this->input('estado');
        $motivo = $this->input('motivo_cancelacion');

        if ($to === 'completada' && new DateTimeImmutable($cita['inicio']) > new DateTimeImmutable()) {
            Flash::set('error', 'Una cita solo puede marcarse como completada cuando ya ocurrió.');
            $this->redirect('/citas/ver', ['id' => $cita['id']]);
        }

        if (!$this->citas->changeStatus((int) $cita['id'], $cita['estado'], $to, $this->actor['id'], $motivo)) {
            Flash::set('error', 'No es posible pasar la cita de «' . (CitaModel::ESTADOS[$cita['estado']] ?? $cita['estado']) . '» a «' . (CitaModel::ESTADOS[$to] ?? $to) . '».');
            $this->redirect('/citas/ver', ['id' => $cita['id']]);
        }

        $evento = ['confirmada' => 'cita_confirmada', 'completada' => 'cita_completada', 'cancelada' => 'cita_cancelada'][$to];
        Bitacora::registrar($evento, 'exito', $this->actor['id'], $this->actor['correo'], $this->resumen($cita) . ($motivo !== '' ? ' · ' . $motivo : ''));
        Flash::set('success', [
            'confirmada' => 'Cita confirmada.',
            'completada' => 'Cita marcada como completada.',
            'cancelada' => 'Cita cancelada. El horario quedó libre.',
        ][$to]);
        $this->redirect('/citas/ver', ['id' => $cita['id']]);
    }

    /**
     * RF05.4: horarios libres en JSON para el formulario.
     */
    public function availability(): void
    {
        $medicoId = (int) $this->query('medico_id', '0');
        $fecha = $this->query('fecha');
        $duracion = (int) $this->query('duracion', '30');
        $slots = $medicoId > 0 && $this->validDate($fecha) && in_array($duracion, self::DURACIONES, true)
            ? $this->citas->slots($medicoId, $fecha, $duracion, (int) $this->query('except', '0'))
            : [];

        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        echo json_encode(['slots' => $slots], JSON_THROW_ON_ERROR);
    }

    private function editable(int $id): array
    {
        $cita = $this->citas->find($id) ?? $this->notFound();

        if (!in_array($cita['estado'], ['pendiente', 'confirmada'], true)) {
            Flash::set('error', 'Solo se pueden modificar citas pendientes o confirmadas.');
            $this->redirect('/citas/ver', ['id' => $id]);
        }

        return $cita;
    }

    private function values(): array
    {
        return [
            'paciente_id' => (int) $this->input('paciente_id', '0'),
            'medico_id' => (int) $this->input('medico_id', '0'),
            'servicio_id' => (int) $this->input('servicio_id', '0'),
            'fecha' => $this->input('fecha'),
            'duracion' => (int) $this->input('duracion', '30'),
            'hora' => $this->input('hora'),
            'motivo' => $this->input('motivo'),
            'observaciones' => $this->input('observaciones'),
        ];
    }

    private function validate(array $values): array
    {
        $errors = [];

        if (!$this->citas->isActive('pacientes', $values['paciente_id'])) {
            $errors['paciente_id'] = 'Selecciona un paciente activo.';
        }

        if (!$this->citas->isActive('medicos', $values['medico_id'])) {
            $errors['medico_id'] = 'Selecciona un médico activo.';
        }

        if (!$this->citas->isActive('servicios', $values['servicio_id'])) {
            $errors['servicio_id'] = 'Selecciona un servicio.';
        }

        if (!in_array($values['duracion'], self::DURACIONES, true)) {
            $errors['duracion'] = 'Duración no válida.';
        }

        if (mb_strlen($values['motivo']) > 255) {
            $errors['motivo'] = 'Máximo 255 caracteres.';
        }

        if (mb_strlen($values['observaciones']) > 2000) {
            $errors['observaciones'] = 'Máximo 2000 caracteres.';
        }

        if (!$this->validDate($values['fecha'])) {
            $errors['fecha'] = 'Selecciona una fecha válida.';
        } elseif (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $values['hora']) !== 1) {
            $errors['hora'] = 'Elige uno de los horarios disponibles.';
        } elseif (!isset($errors['medico_id']) && !isset($errors['duracion'])) {
            [$inicio, $fin] = $this->range($values);

            if (new DateTimeImmutable($inicio) <= new DateTimeImmutable()) {
                $errors['hora'] = 'La cita debe agendarse en una fecha y hora futuras.';
            } elseif (!$this->citas->withinSchedule($values['medico_id'], $inicio, $fin)) {
                $errors['hora'] = 'El horario está fuera de la atención del médico.';
            }
        }

        return $errors;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function range(array $values): array
    {
        $inicio = new DateTimeImmutable($values['fecha'] . ' ' . $values['hora']);

        return [$inicio->format('Y-m-d H:i:s'), $inicio->modify('+' . $values['duracion'] . ' minutes')->format('Y-m-d H:i:s')];
    }

    private function payload(array $values): array
    {
        [$inicio, $fin] = $this->range($values);

        return [
            'paciente_id' => $values['paciente_id'],
            'medico_id' => $values['medico_id'],
            'servicio_id' => $values['servicio_id'],
            'inicio' => $inicio,
            'fin' => $fin,
            'motivo' => $values['motivo'],
            'observaciones' => $values['observaciones'],
            'creado_por' => $this->actor['id'],
        ];
    }

    private function form(array $values, array $errors = [], ?array $cita = null): void
    {
        $slots = $values['medico_id'] > 0 && $this->validDate($values['fecha']) && in_array($values['duracion'], self::DURACIONES, true)
            ? $this->citas->slots($values['medico_id'], $values['fecha'], $values['duracion'], (int) ($values['id'] ?? 0))
            : [];

        $this->render('citas/form', [
            'pageTitle' => $cita ? 'Modificar cita #' . $cita['id'] : 'Agendar cita',
            'values' => $values,
            'errors' => $errors,
            'cita' => $cita,
            'slots' => $slots,
            'pacientes' => $this->citas->pacientesActivos(),
            'medicos' => (new MedicoModel())->active(),
            'servicios' => (new CatalogoModel('servicios'))->active(),
            'duraciones' => self::DURACIONES,
        ]);
    }

    private function resumen(array $cita): string
    {
        return 'Cita #' . $cita['id'] . ' · ' . fecha($cita['inicio']) . ' · ' . $cita['medico'] . ' · paciente #' . $cita['paciente_id'];
    }
}
