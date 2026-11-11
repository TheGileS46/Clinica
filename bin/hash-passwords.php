<?php

declare(strict_types=1);

/**
 * RNF01.2: convierte a password_hash() las contraseñas que sigan en texto plano.
 * Es idempotente: los valores que ya son hash se omiten.
 *
 * Uso: php bin/hash-passwords.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/app/App.php';
App\App::bootstrap();

$db = App\Database::connection();
$rows = $db->query('SELECT id, correo, password FROM usuarios')->fetchAll();
$update = $db->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
$converted = 0;

foreach ($rows as $row) {
    if (password_get_info((string) $row['password'])['algo'] !== null) {
        continue;
    }

    $update->execute(['password' => password_hash((string) $row['password'], PASSWORD_DEFAULT), 'id' => $row['id']]);
    $converted++;
    echo '  · ' . $row['correo'] . PHP_EOL;
}

echo sprintf('Contraseñas convertidas: %d de %d usuarios.%s', $converted, count($rows), PHP_EOL);
