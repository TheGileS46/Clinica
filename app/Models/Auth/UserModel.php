<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Database;

final class UserModel
{
    private const SELECT = 'SELECT
            u.id,
            u.nombre,
            u.correo,
            u.password,
            u.rol_id,
            u.activo,
            r.nombre AS rol,
            m.id AS medico_id
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         LEFT JOIN medicos m ON m.usuario_id = u.id AND m.activo = 1';

    /**
     * Busca por correo sin filtrar el estado, para distinguir en la bitácora
     * cuentas inexistentes de cuentas inactivas.
     */
    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(self::SELECT . ' WHERE u.correo = :correo LIMIT 1');
        $statement->execute(['correo' => $email]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function findActiveById(int $id): ?array
    {
        $statement = Database::connection()->prepare(self::SELECT . ' WHERE u.id = :id AND u.activo = 1 LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    /**
     * RNF01.2: verificación con password_verify(); si el algoritmo por defecto cambió,
     * se vuelve a generar el hash de forma transparente.
     */
    public function passwordMatches(array $user, string $password): bool
    {
        $hash = (string) $user['password'];

        if (!password_verify($password, $hash)) {
            return false;
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $statement = Database::connection()->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
            $statement->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'id' => (int) $user['id']]);
        }

        return true;
    }

    /**
     * Compara contra un hash ficticio para que el tiempo de respuesta no revele
     * si el correo existe.
     */
    public function burnPasswordCheck(string $password): void
    {
        password_verify($password, '$2y$10$x/3z9HcF1BGxEFVBn4Evcu7Ypb44WtIW2IeEIeX0dUzPxSXSiYTlC');
    }
}
