<?php
$byDay = [];
foreach ($horarios as $horario) {
    $byDay[(int) $horario['dia_semana']][] = $horario;
}
?>
<div>
    <a href="<?= e(url('/admin/medicos')) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver a médicos</a>
    <p class="page-eyebrow mt-4">Horarios de atención</p>
    <h1 class="page-title"><?= e($medico['nombre']) ?></h1>
    <p class="mt-1 text-sm text-slate-600"><?= e($medico['especialidad']) ?> · Las citas solo podrán agendarse dentro de estos bloques.</p>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
    <div class="card p-0 sm:p-0">
        <table class="table">
            <thead><tr><th>Día</th><th>Bloques</th></tr></thead>
            <tbody>
                <?php foreach ($dias as $numero => $nombre): ?>
                    <tr>
                        <td class="w-32 font-semibold text-primary"><?= e($nombre) ?></td>
                        <td>
                            <?php if (empty($byDay[$numero])): ?>
                                <span class="text-xs text-slate-400">Sin atención</span>
                            <?php else: ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($byDay[$numero] as $bloque): ?>
                                        <form method="post" action="<?= e(url('/admin/medicos/horarios/eliminar')) ?>" class="inline-flex items-center gap-1 border border-secondary/20 bg-secondary/5 py-1 pl-3 pr-1 text-sm"
                                              data-confirm="¿Eliminar el bloque de <?= e($nombre) ?> <?= e(substr($bloque['hora_inicio'], 0, 5)) ?>–<?= e(substr($bloque['hora_fin'], 0, 5)) ?>?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $medico['id'] ?>">
                                            <input type="hidden" name="horario_id" value="<?= (int) $bloque['id'] ?>">
                                            <span class="font-semibold text-secondary"><?= e(substr($bloque['hora_inicio'], 0, 5)) ?>–<?= e(substr($bloque['hora_fin'], 0, 5)) ?></span>
                                            <button type="submit" class="px-2 text-accent hover:text-accent/70" aria-label="Eliminar bloque">×</button>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form method="post" action="<?= e(url('/admin/medicos/horarios')) ?>" class="card grid content-start gap-4" novalidate>
        <h2 class="text-lg font-black text-primary">Agregar bloque</h2>
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $medico['id'] ?>">
        <div>
            <label for="dia_semana" class="label">Día</label>
            <select id="dia_semana" name="dia_semana" class="input<?= isset($errors['dia_semana']) ? ' input-error' : '' ?>">
                <?php foreach ($dias as $numero => $nombre): ?>
                    <option value="<?= $numero ?>" <?= (int) $old['dia_semana'] === $numero ? 'selected' : '' ?>><?= e($nombre) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['dia_semana'])): ?><p class="field-error"><?= e($errors['dia_semana']) ?></p><?php endif; ?>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="hora_inicio" class="label">Inicio</label>
                <input id="hora_inicio" name="hora_inicio" type="time" step="900" value="<?= e($old['hora_inicio']) ?>" class="input<?= isset($errors['hora_inicio']) ? ' input-error' : '' ?>">
            </div>
            <div>
                <label for="hora_fin" class="label">Fin</label>
                <input id="hora_fin" name="hora_fin" type="time" step="900" value="<?= e($old['hora_fin']) ?>" class="input<?= isset($errors['hora_fin']) ? ' input-error' : '' ?>">
            </div>
        </div>
        <?php foreach (['hora_inicio', 'hora_fin'] as $field): ?>
            <?php if (isset($errors[$field])): ?><p class="field-error"><?= e($errors[$field]) ?></p><?php endif; ?>
        <?php endforeach; ?>
        <button type="submit" class="btn-primary">Agregar horario</button>
    </form>
</div>
