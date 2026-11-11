<?php
$err = static fn (string $field): string => isset($errors[$field]) ? ' input-error' : '';
$msg = static fn (string $field): string => isset($errors[$field]) ? '<p class="field-error">' . e($errors[$field]) . '</p>' : '';
$back = $editing ? url('/pacientes/ver', ['id' => $paciente['id']]) : url('/pacientes');
?>
<div>
    <a href="<?= e($back) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver</a>
    <p class="page-eyebrow mt-4">Recepción</p>
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
</div>

<?php if ($errors !== []): ?>
    <div class="mt-6 border border-accent/25 bg-accent/10 px-4 py-3 text-sm font-semibold text-accent" role="alert">Revisa los campos marcados.</div>
<?php endif; ?>

<form method="post" action="<?= e(url($editing ? '/pacientes/editar' : '/pacientes/crear')) ?>" class="card mt-6 grid max-w-3xl gap-5 sm:grid-cols-2" novalidate>
    <?= csrf_field() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $paciente['id'] ?>"><?php endif; ?>

    <div>
        <label for="nombre" class="label">Nombre(s) *</label>
        <input id="nombre" name="nombre" type="text" maxlength="120" required value="<?= e($paciente['nombre'] ?? '') ?>" class="input<?= $err('nombre') ?>">
        <?= $msg('nombre') ?>
    </div>
    <div>
        <label for="apellidos" class="label">Apellidos *</label>
        <input id="apellidos" name="apellidos" type="text" maxlength="150" required value="<?= e($paciente['apellidos'] ?? '') ?>" class="input<?= $err('apellidos') ?>">
        <?= $msg('apellidos') ?>
    </div>
    <div>
        <label for="telefono" class="label">Teléfono *</label>
        <input id="telefono" name="telefono" type="tel" maxlength="20" required value="<?= e($paciente['telefono'] ?? '') ?>" class="input<?= $err('telefono') ?>" placeholder="33 1234 5678">
        <?= $msg('telefono') ?>
    </div>
    <div>
        <label for="correo" class="label">Correo</label>
        <input id="correo" name="correo" type="email" maxlength="150" value="<?= e($paciente['correo'] ?? '') ?>" class="input<?= $err('correo') ?>">
        <?= $msg('correo') ?>
    </div>
    <div>
        <label for="fecha_nacimiento" class="label">Fecha de nacimiento</label>
        <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" max="<?= date('Y-m-d') ?>" value="<?= e($paciente['fecha_nacimiento'] ?? '') ?>" class="input<?= $err('fecha_nacimiento') ?>">
        <?= $msg('fecha_nacimiento') ?>
    </div>
    <div>
        <label for="curp_dni" class="label">CURP / identificación</label>
        <input id="curp_dni" name="curp_dni" type="text" maxlength="30" value="<?= e($paciente['curp_dni'] ?? '') ?>" class="input uppercase<?= $err('curp_dni') ?>">
        <?= $msg('curp_dni') ?>
    </div>
    <div class="sm:col-span-2">
        <label for="direccion" class="label">Dirección</label>
        <input id="direccion" name="direccion" type="text" maxlength="255" value="<?= e($paciente['direccion'] ?? '') ?>" class="input<?= $err('direccion') ?>">
        <?= $msg('direccion') ?>
    </div>

    <p class="text-xs text-slate-500 sm:col-span-2">Los datos personales se tratan conforme a la LFPDPPP y solo son visibles para el personal autorizado.</p>

    <div class="flex gap-3 border-t border-primary/10 pt-5 sm:col-span-2">
        <button type="submit" class="btn-primary"><?= $editing ? 'Guardar cambios' : 'Registrar paciente' ?></button>
        <a href="<?= e($back) ?>" class="btn-outline">Cancelar</a>
    </div>
</form>
