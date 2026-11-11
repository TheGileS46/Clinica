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
                ['title' => 'Usuarios', 'description' => 'Gestionar cuentas, roles y estado del equipo.', 'path' => '/admin/usuarios'],
                ['title' => 'Servicios y especialidades', 'description' => 'Mantener actualizada la oferta de atención.'],
                ['title' => 'Bitácora', 'description' => 'Revisar accesos, fallos de autenticación y cambios.', 'path' => '/admin/bitacora'],
            ],
        ];
    }
}
