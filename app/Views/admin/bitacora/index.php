<?php
$labels = [
    'login' => 'Inicio de sesión',
    'logout' => 'Cierre de sesión',
    'sesion_expirada' => 'Sesión expirada',
    'acceso_denegado' => 'Acceso denegado',
    'csrf_invalido' => 'Token CSRF inválido',
    'usuario_creado' => 'Usuario creado',
    'usuario_actualizado' => 'Usuario actualizado',
    'usuario_activado' => 'Usuario activado',
    'usuario_desactivado' => 'Usuario desactivado',
];
$label = static fn (string $evento): string => $labels[$evento] ?? ucfirst(str_replace('_', ' ', $evento));
?>
<div>
    <p class="page-eyebrow">Administración</p>
    <h1 class="page-title">Bitácora</h1>
    <p class="mt-1 text-sm text-slate-600">Accesos, fallos de autenticación y cambios administrativos. Nunca se registran contraseñas.</p>
</div>

<form method="get" action="<?= e(url('/admin/bitacora')) ?>" class="card mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_160px_160px_auto] lg:items-end">
    <div>
        <label for="evento" class="label">Evento</label>
        <select id="evento" name="evento" class="input">
            <option value="">Todos</option>
            <?php foreach ($eventos as $evento): ?>
                <option value="<?= e($evento) ?>" <?= $filters['evento'] === $evento ? 'selected' : '' ?>><?= e($label($evento)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="resultado" class="label">Resultado</label>
        <select id="resultado" name="resultado" class="input">
            <option value="">Todos</option>
            <option value="exito" <?= $filters['resultado'] === 'exito' ? 'selected' : '' ?>>Éxito</option>
            <option value="fallo" <?= $filters['resultado'] === 'fallo' ? 'selected' : '' ?>>Fallo</option>
        </select>
    </div>
    <div>
        <label for="correo" class="label">Correo</label>
        <input id="correo" name="correo" type="search" value="<?= e($filters['correo']) ?>" class="input">
    </div>
    <div>
        <label for="desde" class="label">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= e($filters['desde']) ?>" class="input">
    </div>
    <div>
        <label for="hasta" class="label">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= e($filters['hasta']) ?>" class="input">
    </div>
    <button type="submit" class="btn-outline">Filtrar</button>
</form>

<p class="mt-6 text-sm text-slate-500"><?= number_format($total) ?> registro(s)</p>

<div class="card mt-2 overflow-x-auto p-0 sm:p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Evento</th>
                <th>Resultado</th>
                <th>Usuario / correo</th>
                <th>Detalle</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows === []): ?>
                <tr><td colspan="6" class="py-8 text-center text-slate-500">No hay registros con esos filtros.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="whitespace-nowrap text-slate-600"><?= e(fecha($row['created_at'], 'd/m/Y H:i:s')) ?></td>
                    <td class="font-semibold text-primary"><?= e($label($row['evento'])) ?></td>
                    <td><span class="badge <?= $row['resultado'] === 'exito' ? 'badge-ok' : 'badge-danger' ?>"><?= $row['resultado'] === 'exito' ? 'Éxito' : 'Fallo' ?></span></td>
                    <td>
                        <?php if ($row['usuario']): ?><span class="font-semibold"><?= e($row['usuario']) ?></span><br><?php endif; ?>
                        <span class="text-xs text-slate-500"><?= e($row['correo'] ?? '—') ?></span>
                    </td>
                    <td class="text-slate-600"><?= e($row['detalle'] ?? '') ?></td>
                    <td class="whitespace-nowrap text-xs text-slate-500"><?= e($row['ip'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($pages > 1): ?>
    <nav class="mt-4 flex items-center justify-between text-sm" aria-label="Paginación">
        <?php $query = array_filter($filters); ?>
        <?php if ($page > 1): ?>
            <a class="btn-outline" href="<?= e(url('/admin/bitacora', $query + ['pagina' => $page - 1])) ?>">← Anterior</a>
        <?php else: ?><span></span><?php endif; ?>
        <span class="text-slate-500">Página <?= $page ?> de <?= $pages ?></span>
        <?php if ($page < $pages): ?>
            <a class="btn-outline" href="<?= e(url('/admin/bitacora', $query + ['pagina' => $page + 1])) ?>">Siguiente →</a>
        <?php else: ?><span></span><?php endif; ?>
    </nav>
<?php endif; ?>
