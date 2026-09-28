<?php

declare(strict_types=1);

namespace App\Models\Administrador;

final class AdministradorModel
{
    public function dashboard(array $user): array
    {
        return [
            'role' => 'Administrador',
            'title' => 'Panel de administración',
            'message' => 'Desde aquí podrás organizar usuarios, servicios y la operación general de la clínica.',
            'actions' => [
                ['title' => 'Usuarios', 'description' => 'Gestionar cuentas y permisos del equipo.'],
                ['title' => 'Servicios y especialidades', 'description' => 'Mantener actualizada la oferta de atención.'],
                ['title' => 'Configuración', 'description' => 'Revisar los datos generales del sistema.'],
            ],
        ];
    }
}
