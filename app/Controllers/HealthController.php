<?php

declare(strict_types=1);

namespace App\Controllers;

final class HealthController extends Controller
{
    public function index(): void
    {
        $config = require __DIR__ . '/../config/app.php';

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'status' => 'ok',
            'application' => $config['name'],
            'php' => PHP_VERSION,
            'time' => date(DATE_ATOM),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
