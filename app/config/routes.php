<?php

declare(strict_types=1);

use App\Controllers\Administrador\AdministradorController;
use App\Controllers\Administrador\BitacoraController;
use App\Controllers\Administrador\UsuarioController;
use App\Controllers\Auth\LoginController;
use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\Medico\MedicoController;
use App\Controllers\Recepcion\RecepcionController;

// [método, ruta, [controlador, acción]]
return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/health', [HealthController::class, 'index']],

    // Autenticación (RF01.2, RF01.3, RF01.5)
    ['GET', '/login', [LoginController::class, 'index']],
    ['POST', '/login', [LoginController::class, 'login']],
    ['POST', '/logout', [LoginController::class, 'logout']],

    // Paneles por rol (RF02.1)
    ['GET', '/admin', [AdministradorController::class, 'index']],
    ['GET', '/medico', [MedicoController::class, 'index']],
    ['GET', '/recepcion', [RecepcionController::class, 'index']],

    // Usuarios y roles (RF01.1, RF01.4, RF02.2)
    ['GET', '/admin/usuarios', [UsuarioController::class, 'index']],
    ['GET', '/admin/usuarios/crear', [UsuarioController::class, 'create']],
    ['POST', '/admin/usuarios/crear', [UsuarioController::class, 'store']],
    ['GET', '/admin/usuarios/editar', [UsuarioController::class, 'edit']],
    ['POST', '/admin/usuarios/editar', [UsuarioController::class, 'update']],
    ['POST', '/admin/usuarios/estado', [UsuarioController::class, 'toggle']],

    // Bitácora de accesos (RNF01.7)
    ['GET', '/admin/bitacora', [BitacoraController::class, 'index']],
];
