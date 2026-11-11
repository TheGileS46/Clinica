<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="page-eyebrow">Catálogos</p>
        <h1 class="page-title"><?= e($config['title']) ?></h1>
    </div>
    <a href="<?= e(url($config['path'] . '/crear')) ?>" class="btn-primary">+ Nuevo registro</a>
</div>

<form method="get" action="<?= e(url($config['path'])) ?>" class="card mt-6 grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
    <div>
        <label for="q" class="label">Buscar por nombre</label>
        <input id="q" name="q" type="search" value="<?= e($search) ?>" class="input">
    </div>
    <button type="submit" class="btn-outline">Buscar</button>
</form>

<div class="card mt-6 overflow-x-auto p-0 sm:p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <?php if ($config['usage']): ?><th><?= e($config['usage']) ?></th><?php endif; ?>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows === []): ?>
                <tr><td colspan="5" class="py-8 text-center text-slate-500">No hay resultados.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <?php $active = (int) $row['activo'] === 1; ?>
                <tr>
                    <td class="font-semibold text-primary"><?= e($row['nombre']) ?></td>
                    <td class="text-slate-600"><?= e($row['descripcion'] ?? '') ?></td>
                    <?php if ($config['usage']): ?><td><?= (int) $row['en_uso'] ?></td><?php endif; ?>
                    <td><span class="badge <?= $active ? 'badge-ok' : 'badge-off' ?>"><?= $active ? 'Activo' : 'Inactivo' ?></span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= e(url($config['path'] . '/editar', ['id' => $row['id']])) ?>" class="btn-link">Editar</a>
                            <form method="post" action="<?= e(url($config['path'] . '/estado')) ?>"
                                  data-confirm="<?= $active ? '¿Desactivar «' . e($row['nombre']) . '»?' : '¿Activar «' . e($row['nombre']) . '»?' ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="<?= $active ? 'btn-danger' : 'btn-outline py-1.5 text-xs' ?>"><?= $active ? 'Desactivar' : 'Activar' ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
