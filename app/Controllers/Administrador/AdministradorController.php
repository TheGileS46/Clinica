<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Controllers\Controller;
use App\Models\Administrador\AdministradorModel;
use App\Support\Auth;

final class AdministradorController extends Controller
{
    public function index(): void
    {
        $user = Auth::requireRole('Administrador');
        $data = (new AdministradorModel())->dashboard($user);

        $this->render('roles/administrador/dashboard', ['data' => $data, 'pageTitle' => $data['title']]);
    }
}
