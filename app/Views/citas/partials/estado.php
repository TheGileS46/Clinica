<?php
/** @var string $estado */
$clases = [
    'pendiente' => 'badge-warn',
    'confirmada' => 'badge-info',
    'completada' => 'badge-ok',
    'cancelada' => 'badge-off',
    'no_asistio' => 'badge-danger',
    'vencida' => 'badge-danger',
];
$nombres = \App\Models\Citas\CitaModel::ESTADOS;
?>
<span class="badge <?= $clases[$estado] ?? 'badge-off' ?>"><?= e($nombres[$estado] ?? $estado) ?></span>
