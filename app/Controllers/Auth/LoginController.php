<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Support\Auth;
use App\Support\Bitacora;

final class LoginController extends Controller
{
    public function index(): void
    {
        $this->show();
    }

    public function login(): void
    {
        $email = $this->input('email');
        $password = (string) ($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->show($email, 'Escribe un correo válido y tu contraseña.');
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user === null) {
            $userModel->burnPasswordCheck($password);
            Bitacora::registrar('login', 'fallo', null, $email, 'Usuario inexistente');
            $this->show($email, 'Las credenciales no son válidas.');
            return;
        }

        if (!$userModel->passwordMatches($user, $password)) {
            Bitacora::registrar('login', 'fallo', (int) $user['id'], $email, 'Contraseña incorrecta');
            $this->show($email, 'Las credenciales no son válidas.');
            return;
        }

        if ((int) $user['activo'] !== 1) {
            Bitacora::registrar('login', 'fallo', (int) $user['id'], $email, 'Cuenta inactiva');
            $this->show($email, 'Las credenciales no son válidas.');
            return;
        }

        $destination = Auth::routeForRole((string) $user['rol']);

        if ($destination === null) {
            Bitacora::registrar('login', 'fallo', (int) $user['id'], $email, 'Rol sin panel configurado');
            $this->show($email, 'Tu rol aún no tiene un espacio configurado en el sistema.');
            return;
        }

        Auth::login($user);
        Bitacora::registrar('login', 'exito', (int) $user['id'], $email);
        $this->redirect($destination);
    }

    public function logout(): void
    {
        $user = Auth::user();

        if ($user !== null) {
            Bitacora::registrar('logout', 'exito', (int) $user['id'], (string) $user['correo']);
        }

        Auth::logout();
        $this->redirect('/login');
    }

    private function show(string $email = '', ?string $error = null): void
    {
        $this->render('auth/login', [
            'data' => [
                'brand' => 'Clínica de Especialidades',
                'title' => 'Acceso al sistema',
                'message' => 'Ingresa con tus credenciales para continuar.',
            ],
            'email' => $email,
            'error' => $error,
        ], null);
    }
}
