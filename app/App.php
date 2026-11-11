<?php

declare(strict_types=1);

namespace App;

use App\Support\Auth;
use App\Support\Bitacora;
use App\Support\Csrf;
use App\Support\Flash;
use App\Support\Request;
use RuntimeException;
use Throwable;

final class App
{
    private static bool $booted = false;

    private array $config;

    private array $routes;

    public function __construct()
    {
        self::bootstrap();
        $this->config = require __DIR__ . '/config/app.php';
        $this->routes = require __DIR__ . '/config/routes.php';
    }

    /**
     * Carga variables de entorno, autoload, helpers y zona horaria.
     * También se usa desde los scripts de consola (bin/).
     */
    public static function bootstrap(): void
    {
        if (self::$booted) {
            return;
        }

        self::loadEnvironment();
        self::autoload();
        require_once __DIR__ . '/Support/helpers.php';

        $config = require __DIR__ . '/config/app.php';
        date_default_timezone_set($config['timezone']);

        self::$booted = true;
    }

    public function run(): void
    {
        $this->sendSecurityHeaders();

        try {
            Auth::start();
            $path = Request::path();
            $method = Request::method();

            if (Auth::enforceTimeout((int) $this->config['session_timeout'])) {
                Flash::set('info', 'Tu sesión se cerró por inactividad. Vuelve a iniciar sesión.');
            }

            if ($method === 'POST' && !Csrf::verify($_POST['_token'] ?? null)) {
                Bitacora::registrar('csrf_invalido', 'fallo', Auth::user()['id'] ?? null, Auth::user()['correo'] ?? null, $path);
                Flash::set('error', 'El formulario expiró. Intenta de nuevo.');
                header('Location: ' . Auth::route($path) . Request::queryString());
                exit;
            }

            $this->dispatch($method, $path);
        } catch (Throwable $exception) {
            $this->renderError($exception);
        }
    }

    private function dispatch(string $method, string $path): void
    {
        $allowed = [];

        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($routePath !== $path) {
                continue;
            }

            if ($routeMethod !== $method) {
                $allowed[] = $routeMethod;
                continue;
            }

            [$class, $action] = $handler;
            (new $class())->{$action}();
            return;
        }

        if ($allowed !== []) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            throw new RuntimeException('Método no permitido.', 405);
        }

        throw new RuntimeException('Página no encontrada.', 404);
    }

    private function sendSecurityHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'; object-src 'none'");
    }

    private function renderError(Throwable $exception): void
    {
        $code = (int) $exception->getCode();
        $status = $code >= 400 && $code < 600 ? $code : 500;

        if ($status >= 500) {
            error_log((string) $exception);
        }

        $messages = [
            403 => 'No tienes permiso para realizar esta acción.',
            404 => 'Página no encontrada.',
            405 => 'Método no permitido.',
        ];

        $message = $this->config['debug']
            ? $exception->getMessage()
            : ($messages[$status] ?? 'Ocurrió un error interno.');

        if (!headers_sent()) {
            http_response_code($status);
        }

        echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>Error ' . $status . '</title>';
        echo '<h1>Error ' . $status . '</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    private static function autoload(): void
    {
        spl_autoload_register(static function (string $class): void {
            if (!str_starts_with($class, 'App\\')) {
                return;
            }

            $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }

    private static function loadEnvironment(): void
    {
        $file = dirname(__DIR__) . '/.env';

        if (!is_file($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim(trim($value), "\\\"'");

            if ($key !== '' && getenv($key) === false) {
                putenv($key . '=' . $value);
            }
        }
    }
}
