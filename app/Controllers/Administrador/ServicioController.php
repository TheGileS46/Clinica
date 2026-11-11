<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

final class ServicioController extends CatalogoController
{
    protected function config(): array
    {
        return [
            'table' => 'servicios',
            'path' => '/admin/servicios',
            'title' => 'Servicios',
            'singular' => 'Servicio',
            'evento' => 'servicio',
            'usage' => null,
        ];
    }
}
