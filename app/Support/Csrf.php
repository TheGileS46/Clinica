<?php

declare(strict_types=1);

namespace App\Support;

/**
 * RNF01.5: token CSRF por sesión, exigido en toda petición POST.
 */
final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        Auth::start();

        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    public static function verify(mixed $token): bool
    {
        Auth::start();

        return is_string($token)
            && isset($_SESSION[self::KEY])
            && is_string($_SESSION[self::KEY])
            && hash_equals($_SESSION[self::KEY], $token);
    }

    public static function regenerate(): void
    {
        unset($_SESSION[self::KEY]);
    }
}
