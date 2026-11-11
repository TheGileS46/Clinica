<?php

declare(strict_types=1);

namespace App\Support;

final class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Ruta solicitada sin la carpeta base del proyecto (p. ej. /admin/usuarios).
     */
    public static function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = self::basePath();

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    public static function basePath(): string
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

        return ($base === '.' || $base === '/') ? '' : $base;
    }

    public static function queryString(): string
    {
        $query = (string) ($_SERVER['QUERY_STRING'] ?? '');

        return $query === '' ? '' : '?' . $query;
    }

    public static function ip(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return is_string($ip) && filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
    }
}
