<?php
$err = static fn (string $field): string => isset($errors[$field]) ? ' input-error' : '';
$action = $editing ? url('/admin/usuarios/editar') : url('/admin/usuarios/crear');
?>
<div>
    <a href="<?= e(url('/admin/usuarios')) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver a usuarios</a>
    <p class="page-eyebrow mt-4">Administración</p>
    <h1 class="page-title"><?= $editing ? 'Editar usuario' : 'Nuevo usuario' ?></h1>
</div>

<?php if ($errors !== []): ?>
    <div class="mt-6 border border-accent/25 bg-accent/10 px-4 py-3 text-sm font-semibold text-accent" role="alert">
        Revisa los campos marcados.
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="card mt-6 grid max-w-3xl gap-5 sm:grid-cols-2" novalidate>
    <?= csrf_field() ?>
    <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
    <?php endif; ?>

    <div class="sm:col-span-2">
        <label for="nombre" class="label">Nombre completo *</label>
        <input id="nombre" name="nombre" type="text" maxlength="120" required value="<?= e($usuario['nombre'] ?? '') ?>" class="input<?= $err('nombre') ?>">
        <?php if (isset($errors['nombre'])): ?><p class="field-error"><?= e($errors['nombre']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="correo" class="label">Correo institucional *</label>
        <input id="correo" name="correo" type="email" maxlength="150" required autocomplete="off" value="<?= e($usuario['correo'] ?? '') ?>" class="input<?= $err('correo') ?>">
        <?php if (isset($errors['correo'])): ?><p class="field-error"><?= e($errors['correo']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="rol_id" class="label">Rol *</label>
        <select id="rol_id" name="rol_id" required class="input<?= $err('rol_id') ?>" <?= $isSelf ? 'disabled' : '' ?>>
            <option value="">Selecciona…</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= (int) $rol['id'] ?>" <?= (int) ($usuario['rol_id'] ?? 0) === (int) $rol['id'] ? 'selected' : '' ?>><?= e($rol['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($isSelf): ?>
            <input type="hidden" name="rol_id" value="<?= (int) $usuario['rol_id'] ?>">
            <p class="mt-1 text-xs text-slate-500">No puedes cambiar tu propio rol.</p>
        <?php endif; ?>
        <?php if (isset($errors['rol_id'])): ?><p class="field-error"><?= e($errors['rol_id']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="password" class="label">Contraseña <?= $editing ? '' : '*' ?></label>
        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" <?= $editing ? '' : 'required' ?> class="input<?= $err('password') ?>">
        <p class="mt-1 text-xs text-slate-500"><?= $editing ? 'Déjala vacía para conservar la actual.' : 'Mínimo 8 caracteres.' ?></p>
        <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="password_confirmation" class="label">Confirmar contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="input<?= $err('password_confirmation') ?>">
        <?php if (isset($errors['password_confirmation'])): ?><p class="field-error"><?= e($errors['password_confirmation']) ?></p><?php endif; ?>
    </div>

    <?php if (!$editing): ?>
        <label class="flex items-center gap-3 text-sm font-semibold text-primary sm:col-span-2">
            <input type="checkbox" name="activo" value="1" <?= (int) ($usuario['activo'] ?? 1) === 1 ? 'checked' : '' ?> class="h-4 w-4 accent-secondary">
            Cuenta activa (puede iniciar sesión)
        </label>
    <?php endif; ?>

    <div class="flex gap-3 border-t border-primary/10 pt-5 sm:col-span-2">
        <button type="submit" class="btn-primary"><?= $editing ? 'Guardar cambios' : 'Crear usuario' ?></button>
        <a href="<?= e(url('/admin/usuarios')) ?>" class="btn-outline">Cancelar</a>
    </div>
</form>
