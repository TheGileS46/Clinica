<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Controllers\Controller;
use App\Models\Administrador\CatalogoModel;
use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Flash;

/**
 * CRUD con desactivación lógica para catálogos administrados por el Administrador (RF04).
 */
abstract class CatalogoController extends Controller
{
    protected CatalogoModel $model;

    /**
     * @var array<string, mixed>
     */
    protected array $admin;

    /** @return array{table: string, path: string, title: string, singular: string, evento: string, usage: ?string} */
    abstract protected function config(): array;

    public function __construct()
    {
        $this->admin = Auth::requireRole('Administrador');
        $this->model = new CatalogoModel($this->config()['table']);
    }

    public function index(): void
    {
        $search = $this->query('q');

        $this->render('admin/catalogos/index', [
            'pageTitle' => $this->config()['title'],
            'config' => $this->config(),
            'rows' => $this->model->all($search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->form(['nombre' => '', 'descripcion' => '']);
    }

    public function store(): void
    {
        $values = $this->values();
        $errors = $this->validate($values, 0);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $id = $this->model->create($values);
        $this->log('creado', $id, $values['nombre']);
        Flash::set('success', 'Registro creado correctamente.');
        $this->redirect($this->config()['path']);
    }

    public function edit(): void
    {
        $row = $this->model->find($this->idFrom($_GET)) ?? $this->notFound();
        $this->form($row);
    }

    public function update(): void
    {
        $id = $this->idFrom($_POST);
        $this->model->find($id) ?? $this->notFound();
        $values = $this->values() + ['id' => $id];
        $errors = $this->validate($values, $id);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $this->model->update($id, $values);
        $this->log('actualizado', $id, $values['nombre']);
        Flash::set('success', 'Cambios guardados.');
        $this->redirect($this->config()['path']);
    }

    public function toggle(): void
    {
        $id = $this->idFrom($_POST);
        $row = $this->model->find($id) ?? $this->notFound();
        $activar = (int) $row['activo'] !== 1;

        $this->model->setActive($id, $activar);
        $this->log($activar ? 'activado' : 'desactivado', $id, $row['nombre']);
        Flash::set(
            'success',
            $activar ? 'Registro activado.' : 'Registro desactivado. Ya no aparecerá en las nuevas capturas.'
        );
        $this->redirect($this->config()['path']);
    }

    /**
     * @return array<string, mixed>
     */
    private function values(): array
    {
        return [
            'nombre' => $this->input('nombre'),
            'descripcion' => $this->input('descripcion'),
        ];
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, string>
     */
    private function validate(array $values, int $id): array
    {
        $errors = [];

        if ($values['nombre'] === '' || mb_strlen($values['nombre']) > 100) {
            $errors['nombre'] = 'Escribe el nombre (máximo 100 caracteres).';
        } elseif ($this->model->nameExists($values['nombre'], $id)) {
            $errors['nombre'] = 'Ya existe un registro con ese nombre.';
        }

        if (mb_strlen($values['descripcion']) > 1000) {
            $errors['descripcion'] = 'La descripción admite hasta 1000 caracteres.';
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @param array<string, mixed> $row
     */
    private function form(array $row, array $errors = []): void
    {
        $editing = !empty($row['id']);

        $this->render('admin/catalogos/form', [
            'pageTitle' => ($editing ? 'Editar ' : 'Nueva ') . mb_strtolower($this->config()['singular']),
            'config' => $this->config(),
            'row' => $row,
            'errors' => $errors,
            'editing' => $editing,
        ]);
    }

    private function log(string $accion, int $id, string $nombre): void
    {
        Bitacora::registrar(
            $this->config()['evento'] . '_' . $accion,
            'exito',
            $this->admin['id'],
            $this->admin['correo'],
            '#' . $id . ' ' . $nombre
        );
    }
}
