<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="page-eyebrow">Núcleo médico</p>
        <h1 class="page-title">Médicos</h1>
        <p class="mt-1 text-sm text-slate-600">Especialistas, cuenta de acceso vinculada y horarios de atención.</p>
    </div>
    <a href="<?= e(url('/admin/medicos/crear')) ?>" class="btn-primary">+ Nuevo médico</a>
</div>

<form method="get" action="<?= e(url('/admin/medicos')) ?>" class="card mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_220px_180px_auto] lg:items-end">
    <div>
        <label for="q" class="label">Buscar</label>
        <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Nombre, cédula o especialidad" class="input">
    </div>
    <div>
        <label for="especialidad" class="label">Especialidad</label>
        <select id="especialidad" name="especialidad" class="input">
            <option value="">Todas</option>
            <?php foreach ($especialidades as $item): ?>
                <option value="<?= (int) $item['id'] ?>" <?= $especialidad === (int) $item['id'] ? 'selected' : '' ?>><?= e($item['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
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
    <table class="table table-stack">
        <thead>
            <tr>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Contacto</th>
                <th>Cuenta</th>
                <th>Horarios</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($medicos === []): ?>
                <tr><td colspan="7" class="py-8 text-center text-slate-500">No se encontraron médicos.</td></tr>
            <?php endif; ?>
            <?php foreach ($medicos as $medico): ?>
                <?php $active = (int) $medico['activo'] === 1; ?>
                <tr>
                    <td data-label="Médico">
                        <span class="font-semibold text-primary"><?= e($medico['nombre']) ?></span>
                        <?php if ($medico['cedula_profesional']): ?><br><span class="text-xs text-slate-500">Céd. <?= e($medico['cedula_profesional']) ?></span><?php endif; ?>
                    </td>
                    <td data-label="Especialidad"><?= e($medico['especialidad']) ?></td>
                    <td data-label="Contacto" class="text-xs text-slate-600"><?= e($medico['telefono'] ?? '') ?><br><?= e($medico['correo'] ?? '') ?></td>
                    <td data-label="Cuenta" class="text-xs">
                        <?php if ($medico['usuario_correo']): ?>
                            <?= e($medico['usuario_correo']) ?>
                            <?php if ((int) $medico['usuario_activo'] !== 1): ?><span class="badge badge-off">inactiva</span><?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-warn">Sin cuenta</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Horarios">
                        <a href="<?= e(url('/admin/medicos/horarios', ['id' => $medico['id']])) ?>" class="btn-link">
                            <?= (int) $medico['horarios'] > 0 ? (int) $medico['horarios'] . ' bloque(s)' : 'Definir' ?>
                        </a>
                    </td>
                    <td data-label="Estado"><span class="badge <?= $active ? 'badge-ok' : 'badge-off' ?>"><?= $active ? 'Activo' : 'Inactivo' ?></span></td>
                    <td data-label="Acciones">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= e(url('/admin/medicos/editar', ['id' => $medico['id']])) ?>" class="btn-link">Editar</a>
                            <form method="post" action="<?= e(url('/admin/medicos/estado')) ?>"
                                  data-confirm="<?= $active ? '¿Desactivar a ' . e($medico['nombre']) . '? No podrá recibir nuevas citas.' : '¿Activar a ' . e($medico['nombre']) . '?' ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $medico['id'] ?>">
                                <button type="submit" class="<?= $active ? 'btn-danger' : 'btn-outline py-1.5 text-xs' ?>"><?= $active ? 'Desactivar' : 'Activar' ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
