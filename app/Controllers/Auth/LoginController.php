<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Models\Auth\UserModel;
use App\Support\Auth;

final class LoginController
{
    public function index(): void
    {
        $data = [
            'brand' => 'Clínica de Especialidades',
            'title' => 'Acceso al sistema',
            'message' => 'Ingresa con tus credenciales para continuar.',
        ];
        $email = '';
        $error = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
                $error = 'Escribe un correo válido y tu contraseña.';
            } else {
                $userModel = new UserModel();
                $user = $userModel->findActiveByEmail($email);

                if ($user === null || !$userModel->passwordMatches($user, $password)) {
                    $error = 'Las credenciales no son válidas.';
                } else {
                    $destination = Auth::routeForRole((string) $user['rol']);

                    if ($destination === null) {
                        $error = 'Tu rol aún no tiene un espacio configurado en el sistema.';
                    } else {
                        Auth::login($user);
                        header('Location: ' . Auth::route($destination));
                        exit;
                    }
                }
            }
        }

        require __DIR__ . '/../../Views/auth/login.php';
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . Auth::route('/login'));
        exit;
    }
}
