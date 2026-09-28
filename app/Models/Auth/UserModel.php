<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Database;

final class UserModel
{
    public function findActiveByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT
                u.id,
                u.nombre,
                u.correo,
                u.password,
                u.rol_id,
                r.nombre AS rol,
                m.id AS medico_id
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             LEFT JOIN medicos m ON m.usuario_id = u.id AND m.activo = 1
             WHERE u.correo = :correo AND u.activo = 1
             LIMIT 1'
        );
        $statement->execute(['correo' => $email]);

        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function passwordMatches(array $user, string $password): bool
    {
        // Temporalmente la base actual conserva contraseñas en texto plano.
        return hash_equals((string) $user['password'], $password);
    }
}
