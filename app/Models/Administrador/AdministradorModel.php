<?php

declare(strict_types=1);

namespace App\Models\Administrador;

final class AdministradorModel
{
    /**
     * @param array<string, mixed> $user
     * @return array<string, mixed>
     */
    public function dashboard(array $user): array
    {
        return [
            'role' => 'Administrador',
            'title' => 'Panel de administración',
            'message' => 'Desde aquí podrás organizar usuarios, servicios y la operación general de la clínica.',
            'actions' => [
                [
                    'title' => 'Usuarios',
                    'description' => 'Gestionar cuentas, roles y estado del equipo.',
                    'path' => '/admin/usuarios',
                ],
                [
                    'title' => 'Médicos y especialidades',
                    'description' => 'Registrar especialistas, su especialidad y horarios.',
                    'path' => '/admin/medicos',
                ],
                [
                    'title' => 'Bitácora',
                    'description' => 'Revisar accesos, fallos de autenticación y cambios.',
                    'path' => '/admin/bitacora',
                ],
            ],
        ];
    }
}
