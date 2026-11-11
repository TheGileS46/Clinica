<?php
$active = (int) $paciente['activo'] === 1;
$edad = null;
if (!empty($paciente['fecha_nacimiento'])) {
    $edad = (new DateTimeImmutable($paciente['fecha_nacimiento']))->diff(new DateTimeImmutable('today'))->y;
}
$dato = static fn (string $label, ?string $value): string => '<div><dt class="text-xs font-black uppercase tracking-wider text-slate-500">' . e($label) . '</dt><dd class="mt-1 text-sm font-semibold text-text">' . ($value !== null && $value !== '' ? e($value) : '<span class="text-slate-400">—</span>') . '</dd></div>';
?>
<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <a href="<?= e(url('/pacientes')) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver a pacientes</a>
        <p class="page-eyebrow mt-4">Ficha del paciente</p>
        <h1 class="page-title"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></h1>
        <span class="badge mt-2 <?= $active ? 'badge-ok' : 'badge-off' ?>"><?= $active ? 'Activo' : 'Inactivo' ?></span>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= e(url('/pacientes/editar', ['id' => $paciente['id']])) ?>" class="btn-outline">Editar datos</a>
        <form method="post" action="<?= e(url('/pacientes/estado')) ?>"
              data-confirm="<?= $active ? '¿Desactivar a este paciente? Su información se conservará.' : '¿Reactivar a este paciente?' ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $paciente['id'] ?>">
            <button type="submit" class="<?= $active ? 'btn-danger py-2 text-sm' : 'btn-outline' ?>"><?= $active ? 'Desactivar' : 'Reactivar' ?></button>
        </form>
    </div>
</div>

<section class="card mt-6">
    <h2 class="text-lg font-black text-primary">Datos generales y de contacto</h2>
    <dl class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?= $dato('Teléfono', $paciente['telefono']) ?>
        <?= $dato('Correo', $paciente['correo']) ?>
        <?= $dato('Fecha de nacimiento', $paciente['fecha_nacimiento'] ? fecha($paciente['fecha_nacimiento'], 'd/m/Y') . ($edad !== null ? ' (' . $edad . ' años)' : '') : null) ?>
        <?= $dato('CURP / identificación', $paciente['curp_dni']) ?>
        <?= $dato('Dirección', $paciente['direccion']) ?>
        <?= $dato('Registrado', fecha($paciente['created_at'])) ?>
    </dl>
</section>
