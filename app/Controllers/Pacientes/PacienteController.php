<?php

declare(strict_types=1);

namespace App\Controllers\Pacientes;

use App\Controllers\Controller;
use App\Models\Citas\CitaModel;
use App\Models\Pacientes\PacienteModel;
use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Flash;

/**
 * RF03.1 y RF03.2: registro, consulta y edición de pacientes (HU-09).
 * Acceso: Recepción y Administrador (Apéndice C).
 */
final class PacienteController extends Controller
{
    private PacienteModel $pacientes;

    private array $actor;

    public function __construct()
    {
        $this->actor = Auth::requireRole('Recepción', 'Administrador');
        $this->pacientes = new PacienteModel();
    }

    public function index(): void
    {
        $search = $this->query('q');
        $estado = in_array($this->query('estado'), ['activos', 'inactivos', 'todos'], true) ? $this->query('estado') : 'activos';
        $page = max(1, (int) $this->query('pagina', '1'));
        $result = $this->pacientes->search($search, $estado, $page);

        $this->render('pacientes/index', [
            'pageTitle' => 'Pacientes',
            'pacientes' => $result['rows'],
            'total' => $result['total'],
            'page' => $page,
            'pages' => max(1, (int) ceil($result['total'] / PacienteModel::PER_PAGE)),
            'search' => $search,
            'estado' => $estado,
        ]);
    }

    public function show(): void
    {
        $paciente = $this->pacientes->find($this->idFrom($_GET)) ?? $this->notFound();

        $this->render('pacientes/show', [
            'pageTitle' => $paciente['nombre'] . ' ' . $paciente['apellidos'],
            'paciente' => $paciente,
            'citas' => (new CitaModel())->forPaciente((int) $paciente['id']),
        ]);
    }

    public function create(): void
    {
        $this->form([
            'nombre' => '', 'apellidos' => '', 'fecha_nacimiento' => '', 'curp_dni' => '',
            'telefono' => '', 'correo' => '', 'direccion' => '',
        ]);
    }

    public function store(): void
    {
        $values = $this->values();
        $errors = $this->validate($values, 0);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $id = $this->pacientes->create($values);
        $this->log('paciente_creado', $id);
        Flash::set('success', 'Paciente registrado correctamente.');
        $this->redirect('/pacientes/ver', ['id' => $id]);
    }

    public function edit(): void
    {
        $paciente = $this->pacientes->find($this->idFrom($_GET)) ?? $this->notFound();
        $this->form($paciente);
    }

    public function update(): void
    {
        $id = $this->idFrom($_POST);
        $this->pacientes->find($id) ?? $this->notFound();
        $values = $this->values() + ['id' => $id];
        $errors = $this->validate($values, $id);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $this->pacientes->update($id, $values);
        $this->log('paciente_actualizado', $id);
        Flash::set('success', 'Datos del paciente actualizados.');
        $this->redirect('/pacientes/ver', ['id' => $id]);
    }

    public function toggle(): void
    {
        $id = $this->idFrom($_POST);
        $paciente = $this->pacientes->find($id) ?? $this->notFound();
        $activar = (int) $paciente['activo'] !== 1;

        $this->pacientes->setActive($id, $activar);
        $this->log($activar ? 'paciente_activado' : 'paciente_desactivado', $id);
        Flash::set('success', $activar ? 'Paciente reactivado.' : 'Paciente desactivado. Su información se conserva.');
        $this->redirect('/pacientes/ver', ['id' => $id]);
    }

    private function values(): array
    {
        return [
            'nombre' => $this->input('nombre'),
            'apellidos' => $this->input('apellidos'),
            'fecha_nacimiento' => $this->input('fecha_nacimiento'),
            'curp_dni' => mb_strtoupper($this->input('curp_dni')),
            'telefono' => $this->input('telefono'),
            'correo' => mb_strtolower($this->input('correo')),
            'direccion' => $this->input('direccion'),
        ];
    }

    private function validate(array $values, int $id): array
    {
        $errors = [];

        if ($values['nombre'] === '' || mb_strlen($values['nombre']) > 120) {
            $errors['nombre'] = 'Escribe el nombre (máximo 120 caracteres).';
        }

        if ($values['apellidos'] === '' || mb_strlen($values['apellidos']) > 150) {
            $errors['apellidos'] = 'Escribe los apellidos (máximo 150 caracteres).';
        }

        if (!$this->validPhone($values['telefono'])) {
            $errors['telefono'] = 'Escribe un teléfono válido (7 a 20 dígitos).';
        }

        if ($values['correo'] !== '' && (!filter_var($values['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['correo']) > 150)) {
            $errors['correo'] = 'Correo no válido.';
        }

        if ($values['fecha_nacimiento'] !== '') {
            if (!$this->validDate($values['fecha_nacimiento'])) {
                $errors['fecha_nacimiento'] = 'Fecha no válida.';
            } elseif ($values['fecha_nacimiento'] > date('Y-m-d') || $values['fecha_nacimiento'] < '1900-01-01') {
                $errors['fecha_nacimiento'] = 'La fecha debe estar entre 1900 y hoy.';
            }
        }

        if ($values['curp_dni'] !== '') {
            if (preg_match('/^[A-Z0-9-]{5,30}$/', $values['curp_dni']) !== 1) {
                $errors['curp_dni'] = 'Solo letras, números y guiones (5 a 30 caracteres).';
            } elseif ($this->pacientes->curpExists($values['curp_dni'], $id)) {
                $errors['curp_dni'] = 'Ya hay un paciente registrado con este CURP/identificación.';
            }
        }

        if (mb_strlen($values['direccion']) > 255) {
            $errors['direccion'] = 'Máximo 255 caracteres.';
        }

        return $errors;
    }

    private function form(array $paciente, array $errors = []): void
    {
        $editing = !empty($paciente['id']);

        $this->render('pacientes/form', [
            'pageTitle' => $editing ? 'Editar paciente' : 'Nuevo paciente',
            'paciente' => $paciente,
            'errors' => $errors,
            'editing' => $editing,
        ]);
    }

    private function log(string $evento, int $id): void
    {
        Bitacora::registrar($evento, 'exito', $this->actor['id'], $this->actor['correo'], 'Paciente #' . $id);
    }
}
