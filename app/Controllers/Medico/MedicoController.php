<?php

declare(strict_types=1);

namespace App\Controllers\Medico;

use App\Controllers\Controller;
use App\Models\Medico\MedicoModel;
use App\Support\Auth;

final class MedicoController extends Controller
{
    public function index(): void
    {
        $user = Auth::requireRole('Médico');
        $data = (new MedicoModel())->dashboard($user);

        $this->render('roles/medico/dashboard', ['data' => $data, 'pageTitle' => $data['title']]);
    }
}
