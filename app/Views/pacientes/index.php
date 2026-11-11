<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="page-eyebrow">Recepción</p>
        <h1 class="page-title">Pacientes</h1>
        <p class="mt-1 text-sm text-slate-600"><?= number_format($total) ?> paciente(s) encontrados.</p>
    </div>
    <a href="<?= e(url('/pacientes/crear')) ?>" class="btn-primary">+ Nuevo paciente</a>
</div>

<form method="get" action="<?= e(url('/pacientes')) ?>" class="card mt-6 grid gap-4 sm:grid-cols-[1fr_180px_auto] sm:items-end" role="search">
    <div>
        <label for="q" class="label">Buscar</label>
        <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Nombre, teléfono, correo o CURP" class="input" autofocus>
    </div>
    <div>
        <label for="estado" class="label">Estado</label>
        <select id="estado" name="estado" class="input">
            <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
            <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
            <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
        </select>
    </div>
    <button type="submit" class="btn-outline">Buscar</button>
</form>

<div class="card mt-6 overflow-x-auto p-0 sm:p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Nacimiento</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pacientes === []): ?>
                <tr><td colspan="6" class="py-8 text-center text-slate-500">No hay resultados<?= $search !== '' ? ' para «' . e($search) . '»' : '' ?>.</td></tr>
            <?php endif; ?>
            <?php foreach ($pacientes as $paciente): ?>
                <tr>
                    <td>
                        <a href="<?= e(url('/pacientes/ver', ['id' => $paciente['id']])) ?>" class="font-semibold text-primary hover:underline">
                            <?= e($paciente['apellidos'] . ', ' . $paciente['nombre']) ?>
                        </a>
                        <?php if ($paciente['curp_dni']): ?><br><span class="text-xs text-slate-500"><?= e($paciente['curp_dni']) ?></span><?php endif; ?>
                    </td>
                    <td class="whitespace-nowrap"><?= e($paciente['telefono']) ?></td>
                    <td class="text-slate-600"><?= e($paciente['correo'] ?? '') ?></td>
                    <td class="whitespace-nowrap"><?= e(fecha($paciente['fecha_nacimiento'], 'd/m/Y')) ?></td>
                    <td><span class="badge <?= (int) $paciente['activo'] === 1 ? 'badge-ok' : 'badge-off' ?>"><?= (int) $paciente['activo'] === 1 ? 'Activo' : 'Inactivo' ?></span></td>
                    <td class="text-right whitespace-nowrap">
                        <a href="<?= e(url('/pacientes/ver', ['id' => $paciente['id']])) ?>" class="btn-link">Ver ficha</a>
                        <a href="<?= e(url('/pacientes/editar', ['id' => $paciente['id']])) ?>" class="btn-link">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($pages > 1): ?>
    <?php $query = ['q' => $search, 'estado' => $estado]; ?>
    <nav class="mt-4 flex items-center justify-between text-sm" aria-label="Paginación">
        <?php if ($page > 1): ?><a class="btn-outline" href="<?= e(url('/pacientes', $query + ['pagina' => $page - 1])) ?>">← Anterior</a><?php else: ?><span></span><?php endif; ?>
        <span class="text-slate-500">Página <?= $page ?> de <?= $pages ?></span>
        <?php if ($page < $pages): ?><a class="btn-outline" href="<?= e(url('/pacientes', $query + ['pagina' => $page + 1])) ?>">Siguiente →</a><?php else: ?><span></span><?php endif; ?>
    </nav>
<?php endif; ?>
