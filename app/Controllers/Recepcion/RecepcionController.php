<?php

declare(strict_types=1);

namespace App\Controllers\Recepcion;

use App\Models\Recepcion\RecepcionModel;
use App\Support\Auth;

final class RecepcionController
{
    public function index(): void
    {
        $user = Auth::requireRole('Recepción');
        $data = (new RecepcionModel())->dashboard($user);

        require __DIR__ . '/../../Views/roles/recepcion/dashboard.php';
    }
}
