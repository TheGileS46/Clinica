<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Models\Administrador\AdministradorModel;
use App\Support\Auth;

final class AdministradorController
{
    public function index(): void
    {
        $user = Auth::requireRole('Administrador');
        $data = (new AdministradorModel())->dashboard($user);

        require __DIR__ . '/../../Views/roles/administrador/dashboard.php';
    }
}
