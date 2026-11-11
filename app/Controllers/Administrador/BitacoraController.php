<?php

declare(strict_types=1);

namespace App\Controllers\Administrador;

use App\Controllers\Controller;
use App\Models\Administrador\BitacoraModel;
use App\Support\Auth;

final class BitacoraController extends Controller
{
    public function index(): void
    {
        Auth::requireRole('Administrador');

        $filters = [
            'evento' => $this->query('evento'),
            'resultado' => $this->query('resultado'),
            'correo' => $this->query('correo'),
            'desde' => $this->dateOrEmpty($this->query('desde')),
            'hasta' => $this->dateOrEmpty($this->query('hasta')),
        ];
        $page = max(1, (int) $this->query('pagina', '1'));
        $model = new BitacoraModel();
        $result = $model->search($filters, $page);

        $this->render('admin/bitacora/index', [
            'pageTitle' => 'Bitácora',
            'filters' => $filters,
            'rows' => $result['rows'],
            'total' => $result['total'],
            'page' => $page,
            'pages' => max(1, (int) ceil($result['total'] / BitacoraModel::PER_PAGE)),
            'eventos' => $model->eventos(),
        ]);
    }

    private function dateOrEmpty(string $value): string
    {
        return $this->validDate($value) ? $value : '';
    }
}
