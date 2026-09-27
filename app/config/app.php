<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Clinica MVC',
    'environment' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Mexico_City',
];
