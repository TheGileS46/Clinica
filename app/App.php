<?php

declare(strict_types=1);

namespace App;

use App\Controllers\HomeController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Administrador\AdministradorController;
use App\Controllers\Medico\MedicoController;
use App\Controllers\Recepcion\RecepcionController;
use App\Support\Auth;
use RuntimeException;
use Throwable;

final class App
{
    private array $config;

    private array $routes;

    public function __construct()
    {
        $this->loadEnvironment();
        $this->config = require __DIR__ . '/config/app.php';
        $this->routes = require __DIR__ . '/config/routes.php';

        date_default_timezone_set($this->config['timezone']);
    }

    public function run(): void
    {
        $this->autoload();
        Auth::start();

        try {
            $path = $this->path();

            if ($path === $this->routes['health']) {
                $this->json([
                    'status' => 'ok',
                    'application' => $this->config['name'],
                    'php' => PHP_VERSION,
                    'time' => date(DATE_ATOM),
                ]);
            }

            if ($path === $this->routes['login']) {
                (new LoginController())->index();
                return;
            }

            if ($path === $this->routes['logout']) {
                (new LoginController())->logout();
                return;
            }

            if ($path === $this->routes['admin']) {
                (new AdministradorController())->index();
                return;
            }

            if ($path === $this->routes['medico']) {
                (new MedicoController())->index();
                return;
            }

            if ($path === $this->routes['recepcion']) {
                (new RecepcionController())->index();
                return;
            }

            if ($path !== $this->routes['home']) {
                throw new RuntimeException('Página no encontrada.', 404);
            }

            (new HomeController())->index();
        } catch (Throwable $exception) {
            $status = $exception->getCode() >= 400 && $exception->getCode() < 600
                ? $exception->getCode()
                : 500;

            if ($status >= 500) {
                error_log($exception->getMessage());
            }

            http_response_code($status);
            $message = $this->debug()
                ? $exception->getMessage()
                : ($status === 404 ? 'Página no encontrada.' : 'Ocurrió un error interno.');

            echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>Error ' . $status . '</title>';
            echo '<h1>Error ' . $status . '</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    }

    private function autoload(): void
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

    private function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    private function json(array $data): never
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    private function debug(): bool
    {
        return $this->config['debug'];
    }

    private function loadEnvironment(): void
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
