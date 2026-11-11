<?php

declare(strict_types=1);

namespace App\Support;

use App\Database;
use Throwable;

/**
 * RNF01.7: registro de accesos, fallos de autenticación y acciones administrativas.
 * Nunca se guardan contraseñas. Un fallo al registrar no interrumpe la petición.
 */
final class Bitacora
{
    public static function registrar(
        string $evento,
        string $resultado,
        ?int $usuarioId = null,
        ?string $correo = null,
        ?string $detalle = null
    ): void {
        try {
            $statement = Database::connection()->prepare(
                'INSERT INTO bitacora (usuario_id, correo, evento, resultado, detalle, ip, user_agent)
                 VALUES (:usuario_id, :correo, :evento, :resultado, :detalle, :ip, :user_agent)'
            );
            $statement->execute([
                'usuario_id' => $usuarioId,
                'correo' => $correo !== null ? mb_substr($correo, 0, 150) : null,
                'evento' => mb_substr($evento, 0, 50),
                'resultado' => $resultado === 'exito' ? 'exito' : 'fallo',
                'detalle' => $detalle !== null ? mb_substr($detalle, 0, 255) : null,
                'ip' => Request::ip(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT'])
                    ? mb_substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255)
                    : null,
            ]);
        } catch (Throwable $exception) {
            error_log('Bitácora: ' . $exception->getMessage());
        }
    }
}
