<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Controllers\Controller;
use App\Models\Administrador\UsuarioModel;
use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Flash;

/**
 * RF01.1, RF01.4 y RF02.2: alta, edición, activación/desactivación y asignación de rol.
 */
final class UsuarioController extends Controller
{
    private UsuarioModel $usuarios;

    /**
     * @var array<string, mixed>
     */
    private array $admin;

    public function __construct()
    {
        $this->admin = Auth::requireRole('Administrador');
        $this->usuarios = new UsuarioModel();
    }

    public function index(): void
    {
        $search = $this->query('q');
        $estado = $this->query('estado');

        $this->render('admin/usuarios/index', [
            'pageTitle' => 'Usuarios',
            'usuarios' => $this->usuarios->all($search, $estado),
            'search' => $search,
            'estado' => $estado,
            'currentUserId' => $this->admin['id'],
        ]);
    }

    public function create(): void
    {
        $this->form(['nombre' => '', 'correo' => '', 'rol_id' => 0, 'activo' => 1]);
    }

    public function store(): void
    {
        $values = $this->values();
        $errors = $this->validate($values, 0);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $id = $this->usuarios->create($values);
        Bitacora::registrar(
            'usuario_creado',
            'exito',
            $this->admin['id'],
            $this->admin['correo'],
            'Usuario #' . $id . ' (' . $values['correo'] . ')'
        );
        Flash::set('success', 'Usuario creado correctamente.');
        $this->redirect('/admin/usuarios');
    }

    public function edit(): void
    {
        $usuario = $this->usuarios->find($this->idFrom($_GET)) ?? $this->notFound();
        $this->form($usuario);
    }

    public function update(): void
    {
        $id = $this->idFrom($_POST);
        $usuario = $this->usuarios->find($id) ?? $this->notFound();
        $values = $this->values() + ['id' => $id, 'activo' => (int) $usuario['activo']];
        $errors = $this->validate($values, $id);

        if ($errors !== []) {
            $this->form($values, $errors);
            return;
        }

        $this->usuarios->update($id, $values);
        $detalle = 'Usuario #' . $id;

        if ((int) $usuario['rol_id'] !== $values['rol_id']) {
            $detalle .= ' · rol: ' . $usuario['rol'] . ' → ' . $this->usuarios->roleName($values['rol_id']);
        }

        if ($values['password'] !== '') {
            $detalle .= ' · contraseña restablecida';
        }

        Bitacora::registrar('usuario_actualizado', 'exito', $this->admin['id'], $this->admin['correo'], $detalle);
        Flash::set('success', 'Usuario actualizado correctamente.');
        $this->redirect('/admin/usuarios');
    }

    public function toggle(): void
    {
        $id = $this->idFrom($_POST);
        $usuario = $this->usuarios->find($id) ?? $this->notFound();

        if ($id === $this->admin['id']) {
            Flash::set('error', 'No puedes desactivar tu propia cuenta.');
            $this->redirect('/admin/usuarios');
        }

        $activar = (int) $usuario['activo'] !== 1;
        $this->usuarios->setActive($id, $activar);
        Bitacora::registrar(
            $activar ? 'usuario_activado' : 'usuario_desactivado',
            'exito',
            $this->admin['id'],
            $this->admin['correo'],
            'Usuario #' . $id . ' (' . $usuario['correo'] . ')'
        );
        Flash::set('success', $activar ? 'Usuario activado.' : 'Usuario desactivado. Ya no podrá iniciar sesión.');
        $this->redirect('/admin/usuarios');
    }

    /**
     * @return array<string, mixed>
     */
    private function values(): array
    {
        return [
            'nombre' => $this->input('nombre'),
            'correo' => mb_strtolower($this->input('correo')),
            'rol_id' => (int) $this->input('rol_id', '0'),
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0,
        ];
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, string>
     */
    private function validate(array $values, int $id): array
    {
        $errors = [];

        if ($values['nombre'] === '' || mb_strlen($values['nombre']) > 120) {
            $errors['nombre'] = 'Escribe el nombre (máximo 120 caracteres).';
        }

        if (!$this->validEmail($values['correo'])) {
            $errors['correo'] = 'Escribe un correo válido.';
        } elseif ($this->usuarios->emailExists($values['correo'], $id)) {
            $errors['correo'] = 'Ya existe un usuario con ese correo.';
        }

        if ($this->usuarios->roleName($values['rol_id']) === null) {
            $errors['rol_id'] = 'Selecciona un rol.';
        } elseif ($id === $this->admin['id'] && $values['rol_id'] !== $this->admin['rol_id']) {
            $errors['rol_id'] = 'No puedes cambiar tu propio rol.';
        }

        $password = $values['password'];

        if ($id === 0 || $password !== '') {
            if (mb_strlen($password) < 8) {
                $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif ($password !== $values['password_confirmation']) {
                $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
            }
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @param array<string, mixed> $usuario
     */
    private function form(array $usuario, array $errors = []): void
    {
        $editing = !empty($usuario['id']);

        $this->render('admin/usuarios/form', [
            'pageTitle' => $editing ? 'Editar usuario' : 'Nuevo usuario',
            'usuario' => $usuario,
            'roles' => $this->usuarios->roles(),
            'errors' => $errors,
            'editing' => $editing,
            'isSelf' => $editing && (int) $usuario['id'] === $this->admin['id'],
        ]);
    }
}
