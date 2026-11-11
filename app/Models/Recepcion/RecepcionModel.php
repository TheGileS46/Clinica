<?php

declare(strict_types=1);

namespace App\Models\Recepcion;

final class RecepcionModel
{
    public function dashboard(array $user): array
    {
        return [
            'role' => 'Recepción',
            'title' => 'Panel de recepción',
            'message' => 'Este será tu espacio para coordinar pacientes, citas y solicitudes de atención.',
            'actions' => [
                ['title' => 'Pacientes', 'description' => 'Registrar y consultar información de contacto.', 'path' => '/pacientes'],
                ['title' => 'Citas', 'description' => 'Agendar, confirmar, reprogramar y cancelar citas.', 'path' => '/citas'],
                ['title' => 'Solicitudes', 'description' => 'Atender los mensajes recibidos desde el sitio público.'],
            ],
        ];
    }
}
