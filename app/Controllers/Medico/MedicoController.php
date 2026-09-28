<?php

declare(strict_types=1);

namespace App\Controllers\Medico;

use App\Models\Medico\MedicoModel;
use App\Support\Auth;

final class MedicoController
{
    public function index(): void
    {
        $user = Auth::requireRole('Médico');
        $data = (new MedicoModel())->dashboard($user);

        require __DIR__ . '/../../Views/roles/medico/dashboard.php';
    }
}
