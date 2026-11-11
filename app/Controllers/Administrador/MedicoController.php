<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Controllers\Controller;
use App\Models\Administrador\CatalogoModel;
use App\Models\Administrador\MedicoModel;
use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Flash;

/**
 * RF04.1 y RF04.2: registro, consulta y modificación de médicos (HU-08).
 */
final class MedicoController extends Controller
{
    private MedicoModel $medicos;

    private CatalogoModel $especialidades;

    private array $admin;

    public function __construct()
    {
        $this->admin = Auth::requireRole('Administrador');
        $this->medicos = new MedicoModel();
        $this->especialidades = new CatalogoModel('especialidades');
    }

    public function index(): void
    {
        $search = $this->query('q');
        $especialidad = (int) $this->query('especialidad', '0');
        $estado = $this->query('estado');

        $this->render('admin/medicos/index', [
            'pageTitle' => 'Médicos',
            'medicos' => $this->medicos->all($search, $especialidad, $estado),
            'especialidades' => $this->especialidades->all(),
            'search' => $search,
            'especialidad' => $especialidad,
            'estado' => $estado,
        ]);
    }

    public function create(): void
    {
        $this->form(['nombre' => '', 'especialidad_id' => 0, 'usuario_id' => 0, 'cedula_profesional' => '', 'telefono' => '', 'correo' => '']);
    }

    public function store(): void
    {
        $values = $this->values();
        $errors = $this->validate($values, 0, 0);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $id = $this->medicos->create($values);
        Bitacora::registrar('medico_creado', 'exito', $this->admin['id'], $this->admin['correo'], '#' . $id . ' ' . $values['nombre']);
        Flash::set('success', 'Médico registrado. Ahora define sus horarios de atención.');
        $this->redirect('/admin/medicos/horarios', ['id' => $id]);
    }

    public function edit(): void
    {
        $medico = $this->medicos->find($this->idFrom($_GET)) ?? $this->notFound();
        $this->form($medico);
    }

    public function update(): void
    {
        $id = $this->idFrom($_POST);
        $medico = $this->medicos->find($id) ?? $this->notFound();
        $values = $this->values() + ['id' => $id];
        $errors = $this->validate($values, $id, (int) $medico['especialidad_id']);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $this->medicos->update($id, $values);
        Bitacora::registrar('medico_actualizado', 'exito', $this->admin['id'], $this->admin['correo'], '#' . $id . ' ' . $values['nombre']);
        Flash::set('success', 'Datos del médico actualizados.');
        $this->redirect('/admin/medicos');
    }

    public function toggle(): void
    {
        $id = $this->idFrom($_POST);
        $medico = $this->medicos->find($id) ?? $this->notFound();
        $activar = (int) $medico['activo'] !== 1;

        $this->medicos->setActive($id, $activar);
        Bitacora::registrar($activar ? 'medico_activado' : 'medico_desactivado', 'exito', $this->admin['id'], $this->admin['correo'], '#' . $id . ' ' . $medico['nombre']);
        Flash::set('success', $activar ? 'Médico activado.' : 'Médico desactivado. Ya no estará disponible para nuevas citas.');
        $this->redirect('/admin/medicos');
    }

    public function horarios(): void
    {
        $medico = $this->medicos->find($this->idFrom($_GET)) ?? $this->notFound();
        $this->renderHorarios($medico);
    }

    public function addHorario(): void
    {
        $medico = $this->medicos->find($this->idFrom($_POST)) ?? $this->notFound();
        $dia = (int) $this->input('dia_semana', '0');
        $inicio = $this->input('hora_inicio');
        $fin = $this->input('hora_fin');
        $errors = [];

        if (!isset(MedicoModel::DIAS[$dia])) {
            $errors['dia_semana'] = 'Selecciona un día.';
        }

        $validTime = static fn (string $t): bool => preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $t) === 1;

        if (!$validTime($inicio) || !$validTime($fin)) {
            $errors['hora_inicio'] = 'Indica la hora de inicio y fin (HH:MM).';
        } elseif ($fin <= $inicio) {
            $errors['hora_fin'] = 'La hora de fin debe ser posterior a la de inicio.';
        } elseif ($errors === [] && $this->medicos->horarioOverlaps((int) $medico['id'], $dia, $inicio, $fin)) {
            $errors['hora_inicio'] = 'Este bloque se empalma con otro horario del mismo día.';
        }

        if ($errors !== []) {
            $this->renderHorarios($medico, $errors, ['dia_semana' => $dia, 'hora_inicio' => $inicio, 'hora_fin' => $fin]);
            return;
        }

        $this->medicos->addHorario((int) $medico['id'], $dia, $inicio, $fin);
        Flash::set('success', 'Horario agregado.');
        $this->redirect('/admin/medicos/horarios', ['id' => $medico['id']]);
    }

    public function removeHorario(): void
    {
        $medico = $this->medicos->find($this->idFrom($_POST)) ?? $this->notFound();

        if ($this->medicos->removeHorario((int) $medico['id'], $this->idFrom($_POST, 'horario_id'))) {
            Flash::set('success', 'Horario eliminado.');
        }

        $this->redirect('/admin/medicos/horarios', ['id' => $medico['id']]);
    }

    private function renderHorarios(array $medico, array $errors = [], array $old = []): void
    {
        $this->render('admin/medicos/horarios', [
            'pageTitle' => 'Horarios · ' . $medico['nombre'],
            'medico' => $medico,
            'horarios' => $this->medicos->horarios((int) $medico['id']),
            'dias' => MedicoModel::DIAS,
            'errors' => $errors,
            'old' => $old + ['dia_semana' => 1, 'hora_inicio' => '09:00', 'hora_fin' => '14:00'],
        ]);
    }

    private function values(): array
    {
        return [
            'nombre' => $this->input('nombre'),
            'especialidad_id' => (int) $this->input('especialidad_id', '0'),
            'usuario_id' => (int) $this->input('usuario_id', '0'),
            'cedula_profesional' => $this->input('cedula_profesional'),
            'telefono' => $this->input('telefono'),
            'correo' => mb_strtolower($this->input('correo')),
        ];
    }

    private function validate(array $values, int $id, int $currentEspecialidad): array
    {
        $errors = [];

        if ($values['nombre'] === '' || mb_strlen($values['nombre']) > 120) {
            $errors['nombre'] = 'Escribe el nombre del médico (máximo 120 caracteres).';
        }

        // Se permite conservar una especialidad que se desactivó después, pero no asignar una inactiva.
        if (!$this->especialidades->isActive($values['especialidad_id']) && $values['especialidad_id'] !== $currentEspecialidad) {
            $errors['especialidad_id'] = 'Selecciona una especialidad activa.';
        }

        if ($values['usuario_id'] > 0 && !$this->medicos->isAvailableUser($values['usuario_id'], $id)) {
            $errors['usuario_id'] = 'La cuenta no existe, no tiene rol Médico o ya está vinculada a otro médico.';
        }

        if ($values['cedula_profesional'] !== '') {
            if (mb_strlen($values['cedula_profesional']) > 50) {
                $errors['cedula_profesional'] = 'Máximo 50 caracteres.';
            } elseif ($this->medicos->cedulaExists($values['cedula_profesional'], $id)) {
                $errors['cedula_profesional'] = 'Ya existe un médico con esa cédula.';
            }
        }

        if ($values['telefono'] !== '' && !$this->validPhone($values['telefono'])) {
            $errors['telefono'] = 'Teléfono no válido (7 a 20 dígitos).';
        }

        if ($values['correo'] !== '' && (!filter_var($values['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['correo']) > 150)) {
            $errors['correo'] = 'Correo no válido.';
        }

        return $errors;
    }

    private function form(array $medico, array $errors = []): void
    {
        $editing = !empty($medico['id']);

        $this->render('admin/medicos/form', [
            'pageTitle' => $editing ? 'Editar médico' : 'Nuevo médico',
            'medico' => $medico,
            'especialidades' => $this->especialidades->all(),
            'usuarios' => $this->medicos->availableUsers((int) ($medico['id'] ?? 0)),
            'errors' => $errors,
            'editing' => $editing,
        ]);
    }
}
