<?php
$err = static fn (string $field): string => isset($errors[$field]) ? ' input-error' : '';
$msg = static fn (string $field): string => isset($errors[$field]) ? '<p class="field-error">' . e($errors[$field]) . '</p>' : '';
$editing = $cita !== null;
$back = $editing ? url('/citas/ver', ['id' => $cita['id']]) : ($values['paciente_id'] ? url('/pacientes/ver', ['id' => $values['paciente_id']]) : url('/citas'));
?>
<div>
    <a href="<?= e($back) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver</a>
    <p class="page-eyebrow mt-4">Citas</p>
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
    <p class="mt-1 text-sm text-slate-600">Elige médico y fecha; después haz clic en un horario libre para <?= $editing ? 'guardar los cambios' : 'agendar' ?>.</p>
</div>

<?php if ($errors !== []): ?>
    <div class="mt-6 border border-accent/25 bg-accent/10 px-4 py-3 text-sm font-semibold text-accent" role="alert">
        <?= e($errors['hora'] ?? 'Revisa los campos marcados.') ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url($editing ? '/citas/editar' : '/citas/crear')) ?>" class="mt-6 grid gap-6 lg:grid-cols-[1fr_1.1fr]" novalidate
      data-slots="<?= e(url('/citas/disponibilidad')) ?>" data-except="<?= (int) ($values['id'] ?? 0) ?>">
    <!-- Botón por defecto deshabilitado: Enter no agenda accidentalmente el primer horario. -->
    <button type="submit" disabled hidden aria-hidden="true"></button>
    <?= csrf_field() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $cita['id'] ?>"><?php endif; ?>

    <div class="card grid content-start gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="paciente_id" class="label">Paciente *</label>
            <select id="paciente_id" name="paciente_id" required class="input<?= $err('paciente_id') ?>">
                <option value="">Selecciona…</option>
                <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= (int) $paciente['id'] ?>" <?= $values['paciente_id'] === (int) $paciente['id'] ? 'selected' : '' ?>><?= e($paciente['nombre']) ?> · <?= e($paciente['telefono']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $msg('paciente_id') ?>
            <?php if (!$editing): ?><p class="mt-1 text-xs text-slate-500">¿Paciente nuevo? <a href="<?= e(url('/pacientes/crear')) ?>" class="font-bold text-secondary hover:underline">Regístralo primero</a>.</p><?php endif; ?>
        </div>

        <div class="sm:col-span-2">
            <label for="medico_id" class="label">Médico *</label>
            <select id="medico_id" name="medico_id" required class="input<?= $err('medico_id') ?>" data-slot-input>
                <option value="">Selecciona…</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?= (int) $medico['id'] ?>" <?= $values['medico_id'] === (int) $medico['id'] ? 'selected' : '' ?>><?= e($medico['nombre']) ?> · <?= e($medico['especialidad']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $msg('medico_id') ?>
        </div>

        <div>
            <label for="fecha" class="label">Fecha *</label>
            <input id="fecha" name="fecha" type="date" required min="<?= date('Y-m-d') ?>" value="<?= e($values['fecha']) ?>" class="input<?= $err('fecha') ?>" data-slot-input>
            <?= $msg('fecha') ?>
        </div>

        <div>
            <label for="duracion" class="label">Duración</label>
            <select id="duracion" name="duracion" class="input<?= $err('duracion') ?>" data-slot-input>
                <?php foreach ($duraciones as $minutos): ?>
                    <option value="<?= $minutos ?>" <?= $values['duracion'] === $minutos ? 'selected' : '' ?>><?= $minutos ?> min</option>
                <?php endforeach; ?>
            </select>
            <?= $msg('duracion') ?>
        </div>

        <div class="sm:col-span-2">
            <label for="servicio_id" class="label">Servicio *</label>
            <select id="servicio_id" name="servicio_id" required class="input<?= $err('servicio_id') ?>">
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= (int) $servicio['id'] ?>" <?= $values['servicio_id'] === (int) $servicio['id'] ? 'selected' : '' ?>><?= e($servicio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $msg('servicio_id') ?>
        </div>

        <div class="sm:col-span-2">
            <label for="motivo" class="label">Motivo de la consulta</label>
            <input id="motivo" name="motivo" type="text" maxlength="255" value="<?= e($values['motivo']) ?>" class="input<?= $err('motivo') ?>">
            <?= $msg('motivo') ?>
        </div>

        <div class="sm:col-span-2">
            <label for="observaciones" class="label">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2" maxlength="2000" class="input<?= $err('observaciones') ?>"><?= e($values['observaciones']) ?></textarea>
            <?= $msg('observaciones') ?>
        </div>
    </div>

    <div class="card content-start">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-black text-primary">Horarios disponibles</h2>
            <?php if (!$editing): ?>
                <button type="submit" formaction="<?= e(url('/citas/crear')) ?>" formmethod="get" class="btn-link" data-slot-refresh>Actualizar</button>
            <?php endif; ?>
        </div>
        <p class="mt-1 text-xs text-slate-500" data-slot-status>
            <?php if ($values['medico_id'] === 0): ?>
                Selecciona un médico para ver sus horarios.
            <?php elseif ($slots === []): ?>
                No hay horarios libres ese día. Prueba con otra fecha.
            <?php else: ?>
                <?= count($slots) ?> horario(s) libre(s) el <?= e(fecha($values['fecha'], 'd/m/Y')) ?>. Haz clic para <?= $editing ? 'guardar' : 'agendar' ?>.
            <?php endif; ?>
        </p>
        <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4" data-slot-list data-action-label="<?= $editing ? 'Guardar' : 'Agendar' ?>">
            <?php foreach ($slots as $slot): ?>
                <button type="submit" name="hora" value="<?= e($slot) ?>" class="<?= $values['hora'] === $slot ? 'btn-primary' : 'btn-outline' ?> py-2.5" aria-label="<?= $editing ? 'Guardar' : 'Agendar' ?> a las <?= e($slot) ?>"><?= e($slot) ?></button>
            <?php endforeach; ?>
        </div>
        <?= $msg('hora') ?>
    </div>
</form>
