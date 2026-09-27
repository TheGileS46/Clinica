<?php

declare(strict_types=1);

namespace App\Models;

final class HomeModel
{
    public function welcome(): array
    {
        return [
            'title' => 'MVC funcionando',
            'message' => 'La petición pasó por public/index.php, App, un controlador, un modelo y una vista.',
            'environment' => getenv('APP_ENV') ?: 'production',
        ];
    }
}
