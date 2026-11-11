<?php $action = url($config['path'] . ($editing ? '/editar' : '/crear')); ?>
<div>
    <a href="<?= e(url($config['path'])) ?>" class="text-sm font-bold text-secondary hover:underline">← Volver a <?= e(mb_strtolower($config['title'])) ?></a>
    <p class="page-eyebrow mt-4">Catálogos</p>
    <h1 class="page-title"><?= e($pageTitle) ?></h1>
</div>

<form method="post" action="<?= e($action) ?>" class="card mt-6 grid max-w-2xl gap-5" novalidate>
    <?= csrf_field() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><?php endif; ?>

    <div>
        <label for="nombre" class="label">Nombre *</label>
        <input id="nombre" name="nombre" type="text" maxlength="100" required value="<?= e($row['nombre'] ?? '') ?>" class="input<?= isset($errors['nombre']) ? ' input-error' : '' ?>">
        <?php if (isset($errors['nombre'])): ?><p class="field-error"><?= e($errors['nombre']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="descripcion" class="label">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000" class="input<?= isset($errors['descripcion']) ? ' input-error' : '' ?>"><?= e($row['descripcion'] ?? '') ?></textarea>
        <?php if (isset($errors['descripcion'])): ?><p class="field-error"><?= e($errors['descripcion']) ?></p><?php endif; ?>
    </div>

    <div class="flex gap-3 border-t border-primary/10 pt-5">
        <button type="submit" class="btn-primary"><?= $editing ? 'Guardar cambios' : 'Registrar' ?></button>
        <a href="<?= e(url($config['path'])) ?>" class="btn-outline">Cancelar</a>
    </div>
</form>
