<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class View
{
    /**
     * Renderiza app/Views/{template}.php dentro de app/Views/layouts/{layout}.php.
     * Con $layout = null la vista se imprime sola.
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = [], ?string $layout = 'panel'): void
    {
        $file = self::file($template);
        $content = (static function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            require $__file;

            return (string) ob_get_clean();
        })($file, $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        (static function (string $__file, array $__data, string $content): void {
            extract($__data, EXTR_SKIP);
            require $__file;
        })(self::file('layouts/' . $layout), $data, $content);
    }

    private static function file(string $template): string
    {
        $file = dirname(__DIR__) . '/Views/' . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('Vista no encontrada: ' . $template);
        }

        return $file;
    }
}
