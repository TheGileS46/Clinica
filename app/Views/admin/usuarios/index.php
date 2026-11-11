<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="page-eyebrow">Administración</p>
        <h1 class="page-title">Usuarios</h1>
        <p class="mt-1 text-sm text-slate-600">Cuentas del personal, rol asignado y estado de acceso.</p>
    </div>
    <a href="<?= e(url('/admin/usuarios/crear')) ?>" class="btn-primary">+ Nuevo usuario</a>
</div>

<form method="get" action="<?= e(url('/admin/usuarios')) ?>" class="card mt-6 grid gap-4 sm:grid-cols-[1fr_200px_auto] sm:items-end">
    <div>
        <label for="q" class="label">Buscar</label>
        <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Nombre o correo" class="input">
    </div>
    <div>
        <label for="estado" class="label">Estado</label>
        <select id="estado" name="estado" class="input">
            <option value="">Todos</option>
            <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
            <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
        </select>
    </div>
    <button type="submit" class="btn-outline">Filtrar</button>
</form>

<div class="card mt-6 overflow-x-auto p-0 sm:p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios === []): ?>
                <tr><td colspan="5" class="py-8 text-center text-slate-500">No se encontraron usuarios.</td></tr>
            <?php endif; ?>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td class="font-semibold text-primary">
                        <?= e($usuario['nombre']) ?>
                        <?php if ((int) $usuario['id'] === $currentUserId): ?><span class="badge badge-info ml-1">Tú</span><?php endif; ?>
                    </td>
                    <td class="text-slate-600"><?= e($usuario['correo']) ?></td>
                    <td><?= e($usuario['rol']) ?></td>
                    <td>
                        <span class="badge <?= (int) $usuario['activo'] === 1 ? 'badge-ok' : 'badge-off' ?>">
                            <?= (int) $usuario['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= e(url('/admin/usuarios/editar', ['id' => $usuario['id']])) ?>" class="btn-link">Editar</a>
                            <?php if ((int) $usuario['id'] !== $currentUserId): ?>
                                <form method="post" action="<?= e(url('/admin/usuarios/estado')) ?>"
                                      data-confirm="<?= (int) $usuario['activo'] === 1 ? '¿Desactivar a ' . e($usuario['nombre']) . '? No podrá iniciar sesión.' : '¿Activar a ' . e($usuario['nombre']) . '?' ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                                    <button type="submit" class="<?= (int) $usuario['activo'] === 1 ? 'btn-danger' : 'btn-outline py-1.5 text-xs' ?>">
                                        <?= (int) $usuario['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
