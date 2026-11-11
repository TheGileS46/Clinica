<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Support\Auth;
use App\Support\View;
use RuntimeException;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'panel'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path, array $query = []): never
    {
        header('Location: ' . url($path, $query));
        exit;
    }

    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    protected function query(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    protected function idFrom(array $source, string $key = 'id'): int
    {
        $value = filter_var($source[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $value === false ? 0 : (int) $value;
    }

    protected function notFound(): never
    {
        throw new RuntimeException('Registro no encontrado.', 404);
    }

    protected function user(): array
    {
        return Auth::user() ?? [];
    }
}
