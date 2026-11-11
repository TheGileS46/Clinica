<?php

declare(strict_types=1);

// Navegación modular por rol (3.1.1). 'soon' => true muestra el módulo como «Próximamente».
return [
    'administrador' => [
        ['label' => 'Inicio', 'path' => '/admin', 'exact' => true],
        ['label' => 'Usuarios', 'path' => '/admin/usuarios'],
        ['label' => 'Médicos', 'path' => '/admin/medicos'],
        ['label' => 'Especialidades', 'path' => '/admin/especialidades'],
        ['label' => 'Servicios', 'path' => '/admin/servicios'],
        ['label' => 'Pacientes', 'path' => '/pacientes'],
        ['label' => 'Citas', 'path' => '/citas', 'soon' => true],
        ['label' => 'Bitácora', 'path' => '/admin/bitacora'],
    ],
    'recepcion' => [
        ['label' => 'Inicio', 'path' => '/recepcion', 'exact' => true],
        ['label' => 'Pacientes', 'path' => '/pacientes'],
        ['label' => 'Citas', 'path' => '/citas', 'soon' => true],
        ['label' => 'Agenda', 'path' => '/agenda', 'soon' => true],
        ['label' => 'Solicitudes', 'path' => '/solicitudes', 'soon' => true],
    ],
    'medico' => [
        ['label' => 'Inicio', 'path' => '/medico', 'exact' => true],
        ['label' => 'Mi agenda', 'path' => '/agenda', 'soon' => true],
        ['label' => 'Historial clínico', 'path' => '/historial', 'soon' => true],
    ],
];
