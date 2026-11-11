<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Support\Auth;
use App\Support\View;
use RuntimeException;

abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = [], ?string $layout = 'panel'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * @param array<string, mixed> $query
     */
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

    /**
     * @param array<string, mixed> $source
     */
    protected function idFrom(array $source, string $key = 'id'): int
    {
        $value = filter_var($source[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $value === false ? 0 : (int) $value;
    }

    protected function validPhone(string $phone): bool
    {
        return preg_match('/^\+?[0-9 ()-]{7,20}$/', $phone) === 1;
    }

    protected function validEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && mb_strlen($email) <= 150;
    }

    protected function validDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    protected function notFound(): never
    {
        throw new RuntimeException('Registro no encontrado.', 404);
    }

    /**
     * @return array<string, mixed>
     */
    protected function user(): array
    {
        return Auth::user() ?? [];
    }
}
