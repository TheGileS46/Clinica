<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\HomeModel;

final class HomeController
{
    public function index(): void
    {
        $data = (new HomeModel())->welcome();
        require __DIR__ . '/../Views/home.php';
    }
}
