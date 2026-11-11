<?php

declare(strict_types=1);

namespace App\Controllers\Recepcion;

use App\Controllers\Controller;
use App\Models\Recepcion\RecepcionModel;
use App\Support\Auth;

final class RecepcionController extends Controller
{
    public function index(): void
    {
        $user = Auth::requireRole('Recepción');
        $data = (new RecepcionModel())->dashboard($user);

        $this->render('roles/recepcion/dashboard', ['data' => $data, 'pageTitle' => $data['title']]);
    }
}
