<?php

declare(strict_types=1);

namespace App\Models\Medico;

final class MedicoModel
{
    /**
     * @param array<string, mixed> $user
     * @return array<string, mixed>
     */
    public function dashboard(array $user): array
    {
        return [
            'role' => 'Médico',
            'title' => 'Panel médico',
            'message' => 'Este será tu espacio para consultar agenda, pacientes e historial clínico.',
            'actions' => [
                ['title' => 'Mi agenda', 'description' => 'Consultar citas y horarios asignados.'],
                ['title' => 'Pacientes', 'description' => 'Revisar la atención relacionada con tus consultas.'],
                [
                    'title' => 'Historial clínico',
                    'description' => 'Dar seguimiento a la información clínica autorizada.',
                ],
            ],
        ];
    }
}
