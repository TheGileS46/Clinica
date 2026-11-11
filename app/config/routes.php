<?php

declare(strict_types=1);

use App\Controllers\Administrador\AdministradorController;
use App\Controllers\Administrador\BitacoraController;
use App\Controllers\Administrador\EspecialidadController;
use App\Controllers\Administrador\MedicoController as AdminMedicoController;
use App\Controllers\Administrador\ServicioController;
use App\Controllers\Administrador\UsuarioController;
use App\Controllers\Auth\LoginController;
use App\Controllers\HealthController;
use App\Controllers\HomeController;
use App\Controllers\Medico\MedicoController;
use App\Controllers\Pacientes\PacienteController;
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

    // Especialidades y servicios (RF04)
    ['GET', '/admin/especialidades', [EspecialidadController::class, 'index']],
    ['GET', '/admin/especialidades/crear', [EspecialidadController::class, 'create']],
    ['POST', '/admin/especialidades/crear', [EspecialidadController::class, 'store']],
    ['GET', '/admin/especialidades/editar', [EspecialidadController::class, 'edit']],
    ['POST', '/admin/especialidades/editar', [EspecialidadController::class, 'update']],
    ['POST', '/admin/especialidades/estado', [EspecialidadController::class, 'toggle']],
    ['GET', '/admin/servicios', [ServicioController::class, 'index']],
    ['GET', '/admin/servicios/crear', [ServicioController::class, 'create']],
    ['POST', '/admin/servicios/crear', [ServicioController::class, 'store']],
    ['GET', '/admin/servicios/editar', [ServicioController::class, 'edit']],
    ['POST', '/admin/servicios/editar', [ServicioController::class, 'update']],
    ['POST', '/admin/servicios/estado', [ServicioController::class, 'toggle']],

    // Médicos y horarios (RF04.1, RF04.2)
    ['GET', '/admin/medicos', [AdminMedicoController::class, 'index']],
    ['GET', '/admin/medicos/crear', [AdminMedicoController::class, 'create']],
    ['POST', '/admin/medicos/crear', [AdminMedicoController::class, 'store']],
    ['GET', '/admin/medicos/editar', [AdminMedicoController::class, 'edit']],
    ['POST', '/admin/medicos/editar', [AdminMedicoController::class, 'update']],
    ['POST', '/admin/medicos/estado', [AdminMedicoController::class, 'toggle']],
    ['GET', '/admin/medicos/horarios', [AdminMedicoController::class, 'horarios']],
    ['POST', '/admin/medicos/horarios', [AdminMedicoController::class, 'addHorario']],
    ['POST', '/admin/medicos/horarios/eliminar', [AdminMedicoController::class, 'removeHorario']],

    // Pacientes (RF03.1, RF03.2)
    ['GET', '/pacientes', [PacienteController::class, 'index']],
    ['GET', '/pacientes/ver', [PacienteController::class, 'show']],
    ['GET', '/pacientes/crear', [PacienteController::class, 'create']],
    ['POST', '/pacientes/crear', [PacienteController::class, 'store']],
    ['GET', '/pacientes/editar', [PacienteController::class, 'edit']],
    ['POST', '/pacientes/editar', [PacienteController::class, 'update']],
    ['POST', '/pacientes/estado', [PacienteController::class, 'toggle']],

    // Bitácora de accesos (RNF01.7)
    ['GET', '/admin/bitacora', [BitacoraController::class, 'index']],
];
