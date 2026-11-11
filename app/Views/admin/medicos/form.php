<?php
$err = static fn (string $field): string => isset($errors[$field]) ? ' input-error' : '';
$msg = static fn (string $field): string => isset($errors[$field]) ? '<p class="field-error">' . e($errors[$field]) . '</p>' : '';
$action = url($editing ? '/admin/medicos/editar' : '/admin/medicos/crear');
?>
<div>
    <a href="<?= e(url('/admin/medicos')) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver a médicos</a>
    <p class="page-eyebrow mt-4">Núcleo médico</p>
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
</div>

<?php if ($errors !== []): ?>
    <div class="mt-6 border border-accent/25 bg-accent/10 px-4 py-3 text-sm font-semibold text-accent" role="alert">Revisa los campos marcados.</div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="card mt-6 grid max-w-3xl gap-5 sm:grid-cols-2" novalidate>
    <?= csrf_field() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $medico['id'] ?>"><?php endif; ?>

    <div class="sm:col-span-2">
        <label for="nombre" class="label">Nombre completo *</label>
        <input id="nombre" name="nombre" type="text" maxlength="120" required value="<?= e($medico['nombre'] ?? '') ?>" class="input<?= $err('nombre') ?>" placeholder="Dra. Ana López">
        <?= $msg('nombre') ?>
    </div>

    <div>
        <label for="especialidad_id" class="label">Especialidad *</label>
        <select id="especialidad_id" name="especialidad_id" required class="input<?= $err('especialidad_id') ?>">
            <option value="">Selecciona…</option>
            <?php foreach ($especialidades as $item): ?>
                <?php $selected = (int) ($medico['especialidad_id'] ?? 0) === (int) $item['id']; ?>
                <?php if ((int) $item['activo'] === 1 || $selected): ?>
                    <option value="<?= (int) $item['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= e($item['nombre']) ?><?= (int) $item['activo'] === 1 ? '' : ' (inactiva)' ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <?= $msg('especialidad_id') ?>
    </div>

    <div>
        <label for="cedula_profesional" class="label">Cédula profesional</label>
        <input id="cedula_profesional" name="cedula_profesional" type="text" maxlength="50" value="<?= e($medico['cedula_profesional'] ?? '') ?>" class="input<?= $err('cedula_profesional') ?>">
        <?= $msg('cedula_profesional') ?>
    </div>

    <div>
        <label for="telefono" class="label">Teléfono</label>
        <input id="telefono" name="telefono" type="tel" maxlength="20" value="<?= e($medico['telefono'] ?? '') ?>" class="input<?= $err('telefono') ?>">
        <?= $msg('telefono') ?>
    </div>

    <div>
        <label for="correo" class="label">Correo de contacto</label>
        <input id="correo" name="correo" type="email" maxlength="150" value="<?= e($medico['correo'] ?? '') ?>" class="input<?= $err('correo') ?>">
        <?= $msg('correo') ?>
    </div>

    <div class="sm:col-span-2">
        <label for="usuario_id" class="label">Cuenta de acceso vinculada</label>
        <select id="usuario_id" name="usuario_id" class="input<?= $err('usuario_id') ?>">
            <option value="0">Sin cuenta (no podrá iniciar sesión)</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= (int) $usuario['id'] ?>" <?= (int) ($medico['usuario_id'] ?? 0) === (int) $usuario['id'] ? 'selected' : '' ?>>
                    <?= e($usuario['nombre']) ?> · <?= e($usuario['correo']) ?><?= (int) $usuario['activo'] === 1 ? '' : ' (inactiva)' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="mt-1 text-xs text-slate-500">Solo aparecen cuentas con rol Médico sin vincular. Créalas en <a class="font-bold text-secondary hover:underline" href="<?= e(url('/admin/usuarios/crear')) ?>">Usuarios</a>.</p>
        <?= $msg('usuario_id') ?>
    </div>

    <div class="flex gap-3 border-t border-primary/10 pt-5 sm:col-span-2">
        <button type="submit" class="btn-primary"><?= $editing ? 'Guardar cambios' : 'Registrar médico' ?></button>
        <a href="<?= e(url('/admin/medicos')) ?>" class="btn-outline">Cancelar</a>
    </div>
</form>
