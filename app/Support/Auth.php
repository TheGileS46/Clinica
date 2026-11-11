<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Auth\UserModel;

final class Auth
{
    private const LAST_ACTIVITY = '_last_activity';

    /**
     * RNF01.6: cookie de sesión HttpOnly, SameSite y Secure cuando hay HTTPS.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('CLINICASESSID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => Request::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        Csrf::regenerate();

        $_SESSION['auth_user'] = self::sessionData($user);
        $_SESSION[self::LAST_ACTIVITY] = time();
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
        // Se abre una sesión nueva (ID nuevo por use_strict_mode) para mensajes flash.
        self::start();
    }

    /**
     * RNF01.4: cierra la sesión si se superó el tiempo de inactividad.
     * Devuelve true cuando la sesión expiró en esta petición.
     */
    public static function enforceTimeout(int $seconds): bool
    {
        self::start();
        $user = self::user();

        if ($user === null) {
            return false;
        }

        $last = (int) ($_SESSION[self::LAST_ACTIVITY] ?? 0);

        if ($last > 0 && (time() - $last) > $seconds) {
            Bitacora::registrar('sesion_expirada', 'exito', $user['id'], $user['correo']);
            self::logout();
            return true;
        }

        $_SESSION[self::LAST_ACTIVITY] = time();

        return false;
    }

    public static function user(): ?array
    {
        self::start();

        return isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])
            ? $_SESSION['auth_user']
            : null;
    }

    /**
     * RF02.3: exige sesión y uno de los roles indicados. El usuario se vuelve a leer
     * de la base para aplicar al momento desactivaciones o cambios de rol.
     */
    public static function requireRole(string ...$roles): array
    {
        $user = self::user();

        if ($user === null) {
            self::redirect('/login');
        }

        $fresh = (new UserModel())->findActiveById((int) $user['id']);

        if ($fresh === null) {
            Bitacora::registrar('acceso_denegado', 'fallo', (int) $user['id'], (string) $user['correo'], 'Cuenta inactiva o eliminada');
            self::logout();
            Flash::set('error', 'Tu cuenta ya no está activa. Contacta al administrador.');
            self::redirect('/login');
        }

        $_SESSION['auth_user'] = $user = self::sessionData($fresh);
        $allowed = array_map(self::normalizeRole(...), $roles);

        if (!in_array(self::normalizeRole($user['rol']), $allowed, true)) {
            Bitacora::registrar('acceso_denegado', 'fallo', $user['id'], $user['correo'], Request::path());
            Flash::set('error', 'No tienes permiso para acceder a esa sección.');
            self::redirect('/login');
        }

        return $user;
    }

    public static function hasRole(string ...$roles): bool
    {
        $user = self::user();

        return $user !== null
            && in_array(self::normalizeRole($user['rol']), array_map(self::normalizeRole(...), $roles), true);
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
        $path = '/' . trim($path, '/');

        if ($path === '//') {
            $path = '/';
        }

        $base = Request::basePath();

        return $base === '' ? $path : $base . ($path === '/' ? '/' : $path);
    }

    public static function normalizeRole(string $role): string
    {
        return strtr(mb_strtolower(trim($role)), [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
        ]);
    }

    private static function sessionData(array $user): array
    {
        return [
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

    private static function redirect(string $path): never
    {
        header('Location: ' . self::route($path));
        exit;
    }
}
