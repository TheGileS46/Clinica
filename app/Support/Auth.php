<?php

declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);

        $_SESSION['auth_user'] = [
            'id' => (int) $user['id'],
            'nombre' => (string) $user['nombre'],
            'correo' => (string) $user['correo'],
            'rol_id' => (int) $user['rol_id'],
            'rol' => (string) $user['rol'],
            'medico_id' => isset($user['medico_id']) && $user['medico_id'] !== null
                ? (int) $user['medico_id']
                : null,
        ];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function user(): ?array
    {
        self::start();
        return isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])
            ? $_SESSION['auth_user']
            : null;
    }

    public static function requireRole(string $role): array
    {
        $user = self::user();
        $requestedRole = self::normalizeRole($role);

        if ($user === null || self::normalizeRole((string) ($user['rol'] ?? '')) !== $requestedRole) {
            header('Location: ' . self::route('/login'));
            exit;
        }

        return $user;
    }

    public static function routeForRole(string $role): ?string
    {
        return match (self::normalizeRole($role)) {
            'administrador' => '/admin',
            'medico' => '/medico',
            'recepcion' => '/recepcion',
            default => null,
        };
    }

    public static function route(string $path): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $path = '/' . trim($path, '/');

        if ($path === '//') {
            $path = '/';
        }

        return ($base === '' || $base === '.') ? $path : $base . ($path === '/' ? '/' : $path);
    }

    private static function normalizeRole(string $role): string
    {
        return strtr(strtolower(trim($role)), [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
        ]);
    }
}
