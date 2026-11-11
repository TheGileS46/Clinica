<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

final class EspecialidadController extends CatalogoController
{
    protected function config(): array
    {
        return [
            'table' => 'especialidades',
            'path' => '/admin/especialidades',
            'title' => 'Especialidades',
            'singular' => 'Especialidad',
            'evento' => 'especialidad',
            'usage' => 'Médicos activos',
        ];
    }
}
