<?php
$badge = static function (string $estado): void {
    require dirname(__DIR__) . '/citas/partials/estado.php';
};
$query = array_filter($filters);
?>
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="page-eyebrow">Recepción</p>
        <h1 class="page-title">Citas</h1>
        <p class="mt-1 text-sm text-slate-600">
            <?= number_format($total) ?> cita(s) del <?= e(fecha($filters['desde'], 'd/m/Y')) ?><?= $filters['hasta'] !== $filters['desde'] ? ' al ' . e(fecha($filters['hasta'], 'd/m/Y')) : '' ?>.
        </p>
    </div>
    <a href="<?= e(url('/citas/crear')) ?>" class="btn-primary">+ Agendar cita</a>
</div>

<form method="get" action="<?= e(url('/citas')) ?>" class="card mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-[150px_150px_1fr_160px_1fr_auto] lg:items-end">
    <div>
        <label for="desde" class="label">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= e($filters['desde']) ?>" class="input">
    </div>
    <div>
        <label for="hasta" class="label">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= e($filters['hasta']) ?>" class="input">
    </div>
    <div>
        <label for="medico_id" class="label">Médico</label>
        <select id="medico_id" name="medico_id" class="input">
            <option value="">Todos</option>
            <?php foreach ($medicos as $medico): ?>
                <option value="<?= (int) $medico['id'] ?>" <?= $filters['medico_id'] === (int) $medico['id'] ? 'selected' : '' ?>><?= e($medico['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="estado" class="label">Estado</label>
        <select id="estado" name="estado" class="input">
            <option value="">Todos</option>
            <?php foreach ($estados as $clave => $nombre): ?>
                <option value="<?= e($clave) ?>" <?= $filters['estado'] === $clave ? 'selected' : '' ?>><?= e($nombre) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="q" class="label">Paciente</label>
        <input id="q" name="q" type="search" value="<?= e($filters['q']) ?>" placeholder="Nombre o teléfono" class="input">
    </div>
    <button type="submit" class="btn-outline">Filtrar</button>
</form>

<div class="card mt-6 overflow-x-auto p-0 sm:p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Fecha y hora</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($citas === []): ?>
                <tr><td colspan="6" class="py-8 text-center text-slate-500">No hay citas con esos filtros.</td></tr>
            <?php endif; ?>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td class="whitespace-nowrap">
                        <span class="font-semibold text-primary"><?= e(fecha($cita['inicio'], 'd/m/Y')) ?></span><br>
                        <span class="text-xs text-slate-500"><?= e(fecha($cita['inicio'], 'H:i')) ?>–<?= e(fecha($cita['fin'], 'H:i')) ?></span>
                    </td>
                    <td>
                        <a href="<?= e(url('/pacientes/ver', ['id' => $cita['paciente_id']])) ?>" class="font-semibold hover:underline"><?= e($cita['paciente']) ?></a><br>
                        <span class="text-xs text-slate-500"><?= e($cita['paciente_telefono']) ?></span>
                    </td>
                    <td><?= e($cita['medico']) ?><br><span class="text-xs text-slate-500"><?= e($cita['especialidad']) ?></span></td>
                    <td class="text-slate-600"><?= e($cita['servicio'] ?? '—') ?></td>
                    <td><?php $badge($cita['estado']); ?></td>
                    <td class="text-right"><a href="<?= e(url('/citas/ver', ['id' => $cita['id']])) ?>" class="btn-link">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($pages > 1): ?>
    <nav class="mt-4 flex items-center justify-between text-sm" aria-label="Paginación">
        <?php if ($page > 1): ?><a class="btn-outline" href="<?= e(url('/citas', $query + ['pagina' => $page - 1])) ?>">← Anterior</a><?php else: ?><span></span><?php endif; ?>
        <span class="text-slate-500">Página <?= $page ?> de <?= $pages ?></span>
        <?php if ($page < $pages): ?><a class="btn-outline" href="<?= e(url('/citas', $query + ['pagina' => $page + 1])) ?>">Siguiente →</a><?php else: ?><span></span><?php endif; ?>
    </nav>
<?php endif; ?>
