<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Mensajes de una sola lectura entre redirecciones (patrón PRG).
 */
final class Flash
{
    private const KEY = '_flash';

    public static function set(string $type, string $message): void
    {
        Auth::start();
        $_SESSION[self::KEY][] = ['type' => $type, 'message' => $message];
    }

    /**
     * @return list<array{type: string, message: string}>
     */
    public static function pull(): array
    {
        Auth::start();
        $messages = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);

        return is_array($messages) ? $messages : [];
    }
}
