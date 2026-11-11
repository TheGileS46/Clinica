<?php

declare(strict_types=1);

use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Request;

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url')) {
    /**
     * URL interna respetando la carpeta base del proyecto.
     * @param array<string, mixed> $query
     */
    function url(string $path, array $query = []): string
    {
        $query = array_filter($query, static fn ($value) => $value !== null && $value !== '');

        return Auth::route($path) . ($query === [] ? '' : '?' . http_build_query($query));
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return Request::basePath() . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('is_current')) {
    /**
     * Indica si la ruta actual es $path o una subruta de ella.
     */
    function is_current(string $path, bool $exact = false): bool
    {
        $current = Request::path();

        return $exact ? $current === $path : ($current === $path || str_starts_with($current, rtrim($path, '/') . '/'));
    }
}

if (!function_exists('fecha')) {
    function fecha(?string $value, string $format = 'd/m/Y H:i'): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        $time = strtotime($value);

        return $time === false ? '—' : date($format, $time);
    }
}
