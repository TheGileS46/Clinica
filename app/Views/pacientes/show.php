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
        <?php if ($active): ?>
            <a href="<?= e(url('/citas/crear', ['paciente_id' => $paciente['id']])) ?>" class="btn-primary">+ Agendar cita</a>
        <?php endif; ?>
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

<?php
$ahora = date('Y-m-d H:i:s');
$proximas = array_reverse(array_values(array_filter($citas, static fn (array $c): bool => $c['inicio'] >= $ahora)));
$anteriores = array_values(array_filter($citas, static fn (array $c): bool => $c['inicio'] < $ahora));
?>
<?php foreach (['Próximas citas' => $proximas, 'Citas anteriores' => $anteriores] as $titulo => $grupo): ?>
    <section class="card mt-6 p-0 sm:p-0">
        <h2 class="px-5 pt-5 text-lg font-black text-primary sm:px-6"><?= e($titulo) ?> <span class="text-sm font-semibold text-slate-400">(<?= count($grupo) ?>)</span></h2>
        <div class="mt-3 overflow-x-auto">
            <table class="table">
                <thead><tr><th>Fecha y hora</th><th>Médico</th><th>Servicio</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                    <?php if ($grupo === []): ?>
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Sin registros.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($grupo as $cita): ?>
                        <tr>
                            <td class="whitespace-nowrap font-semibold text-primary"><?= e(fecha($cita['inicio'])) ?></td>
                            <td><?= e($cita['medico']) ?></td>
                            <td class="text-slate-600"><?= e($cita['servicio'] ?? '—') ?></td>
                            <td><?php $estado = $cita['estado']; require dirname(__DIR__) . '/citas/partials/estado.php'; ?></td>
                            <td class="text-right"><a class="btn-link" href="<?= e(url('/citas/ver', ['id' => $cita['id']])) ?>">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endforeach; ?>
