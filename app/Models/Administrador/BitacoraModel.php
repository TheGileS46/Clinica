<?php

declare(strict_types=1);

namespace App\Models\Administrador;

use App\Database;

final class BitacoraModel
{
    public const PER_PAGE = 50;

    /**
     * @return array{rows: array, total: int}
     */
    public function search(array $filters, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($filters['evento'] !== '') {
            $where[] = 'b.evento = :evento';
            $params['evento'] = $filters['evento'];
        }

        if ($filters['resultado'] === 'exito' || $filters['resultado'] === 'fallo') {
            $where[] = 'b.resultado = :resultado';
            $params['resultado'] = $filters['resultado'];
        }

        if ($filters['correo'] !== '') {
            $where[] = 'b.correo LIKE :correo';
            $params['correo'] = '%' . $filters['correo'] . '%';
        }

        if ($filters['desde'] !== '') {
            $where[] = 'b.created_at >= :desde';
            $params['desde'] = $filters['desde'] . ' 00:00:00';
        }

        if ($filters['hasta'] !== '') {
            $where[] = 'b.created_at <= :hasta';
            $params['hasta'] = $filters['hasta'] . ' 23:59:59';
        }

        $whereSql = implode(' AND ', $where);
        $db = Database::connection();

        $count = $db->prepare('SELECT COUNT(*) FROM bitacora b WHERE ' . $whereSql);
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $offset = max(0, ($page - 1) * self::PER_PAGE);
        $statement = $db->prepare(
            'SELECT b.id, b.created_at, b.evento, b.resultado, b.correo, b.detalle, b.ip, u.nombre AS usuario
             FROM bitacora b
             LEFT JOIN usuarios u ON u.id = b.usuario_id
             WHERE ' . $whereSql . '
             ORDER BY b.id DESC
             LIMIT ' . self::PER_PAGE . ' OFFSET ' . $offset
        );
        $statement->execute($params);

        return ['rows' => $statement->fetchAll(), 'total' => $total];
    }

    public function eventos(): array
    {
        return Database::connection()->query('SELECT DISTINCT evento FROM bitacora ORDER BY evento')->fetchAll(\PDO::FETCH_COLUMN);
    }
}
