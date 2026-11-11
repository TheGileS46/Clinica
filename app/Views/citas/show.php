<?php
$estado = $cita['estado'];
$dato = static fn (string $label, ?string $value): string => '<div><dt class="text-xs font-black uppercase tracking-wider text-slate-500">' . e($label) . '</dt><dd class="mt-1 text-sm font-semibold text-text">' . ($value !== null && $value !== '' ? e($value) : '<span class="text-slate-400">—</span>') . '</dd></div>';
$editable = in_array($estado, ['pendiente', 'confirmada'], true);
?>
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <a href="<?= e(url('/citas', ['desde' => fecha($cita['inicio'], 'Y-m-d')])) ?>" class="text-sm font-bold text-secondary hover:underline">← Citas del día</a>
        <p class="page-eyebrow mt-4">Cita #<?= (int) $cita['id'] ?></p>
        <h1 class="page-title"><?= e(fecha($cita['inicio'], 'd/m/Y · H:i')) ?>–<?= e(fecha($cita['fin'], 'H:i')) ?></h1>
        <div class="mt-2"><?php require __DIR__ . '/partials/estado.php'; ?></div>
    </div>
    <?php if ($editable): ?>
        <a href="<?= e(url('/citas/editar', ['id' => $cita['id']])) ?>" class="btn-outline">Modificar / reprogramar</a>
    <?php endif; ?>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
    <section class="card">
        <h2 class="text-lg font-black text-primary">Datos de la cita</h2>
        <dl class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-black uppercase tracking-wider text-slate-500">Paciente</dt>
                <dd class="mt-1 text-sm font-semibold"><a class="text-secondary hover:underline" href="<?= e(url('/pacientes/ver', ['id' => $cita['paciente_id']])) ?>"><?= e($cita['paciente']) ?></a> · <?= e($cita['paciente_telefono']) ?></dd>
            </div>
            <?= $dato('Médico', $cita['medico'] . ' · ' . $cita['especialidad']) ?>
            <?= $dato('Servicio', $cita['servicio']) ?>
            <?= $dato('Motivo', $cita['motivo']) ?>
            <?= $dato('Observaciones', $cita['observaciones']) ?>
            <?= $dato('Agendada por', ($cita['creado_por_nombre'] ?? '—') . ' · ' . fecha($cita['created_at'])) ?>
            <?php if ($estado === 'cancelada'): ?>
                <?= $dato('Cancelada por', $cita['cancelada_por_nombre']) ?>
                <?= $dato('Motivo de cancelación', $cita['motivo_cancelacion']) ?>
            <?php endif; ?>
        </dl>
    </section>

    <section class="card content-start">
        <h2 class="text-lg font-black text-primary">Estado</h2>
        <?php if ($transiciones === []): ?>
            <p class="mt-2 text-sm text-slate-600">Esta cita está en un estado final y ya no admite cambios.</p>
        <?php endif; ?>

        <div class="mt-4 grid gap-3">
            <?php if (in_array('confirmada', $transiciones, true)): ?>
                <form method="post" action="<?= e(url('/citas/estado')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                    <input type="hidden" name="estado" value="confirmada">
                    <button type="submit" class="btn-primary w-full">Confirmar asistencia</button>
                </form>
            <?php endif; ?>

            <?php if (in_array('completada', $transiciones, true)): ?>
                <form method="post" action="<?= e(url('/citas/estado')) ?>" data-confirm="¿Marcar la cita como completada?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                    <input type="hidden" name="estado" value="completada">
                    <button type="submit" class="btn-primary w-full" <?= $yaOcurrio ? '' : 'disabled title="Disponible cuando llegue la hora de la cita"' ?>>Marcar como completada</button>
                </form>
            <?php endif; ?>

            <?php if (in_array('cancelada', $transiciones, true)): ?>
                <form method="post" action="<?= e(url('/citas/estado')) ?>" class="grid gap-2 border-t border-primary/10 pt-4" data-confirm="¿Cancelar esta cita? El horario quedará libre.">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                    <input type="hidden" name="estado" value="cancelada">
                    <label for="motivo_cancelacion" class="label">Motivo de cancelación</label>
                    <input id="motivo_cancelacion" name="motivo_cancelacion" type="text" maxlength="255" class="input mt-0" placeholder="Opcional">
                    <button type="submit" class="btn-danger py-2.5 text-sm">Cancelar cita</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>
