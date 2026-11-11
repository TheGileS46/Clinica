<?php
/** @var string $content */
$authUser = \App\Support\Auth::user() ?? ['nombre' => '', 'correo' => '', 'rol' => ''];
$menu = (require dirname(__DIR__, 2) . '/config/menu.php')[\App\Support\Auth::normalizeRole((string) $authUser['rol'])] ?? [];
$pageTitle = $pageTitle ?? 'Panel';
$flashes = \App\Support\Flash::pull();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004E64">
    <title><?= e($pageTitle) ?> · Clínica</title>
    <link rel="icon" type="image/png" href="<?= e(asset('logos/Dientes_logo_blanco.png')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-primary focus:px-4 focus:py-3 focus:text-white">Saltar al contenido</a>

    <header class="border-b border-primary/10 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-4 py-4 sm:px-8">
            <a href="<?= e(url('/')) ?>" aria-label="Ir al inicio">
                <img src="<?= e(asset('logos/logo_letras.png')) ?>" alt="Clínica" class="h-9 w-auto max-w-[160px] object-contain">
            </a>
            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-bold text-primary"><?= e($authUser['nombre']) ?></p>
                    <p class="text-xs text-slate-500"><?= e($authUser['rol']) ?> · <?= e($authUser['correo']) ?></p>
                </div>
                <form action="<?= e(url('/logout')) ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-outline">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <nav class="border-t border-primary/10" aria-label="Módulos">
            <ul class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 sm:px-8">
                <?php foreach ($menu as $item): ?>
                    <?php $active = is_current($item['path'], $item['exact'] ?? false); ?>
                    <li class="shrink-0">
                        <?php if (!empty($item['soon'])): ?>
                            <span class="inline-flex items-center gap-2 px-3 py-3 text-sm font-semibold text-slate-400" title="Próximamente">
                                <?= e($item['label']) ?>
                                <span class="text-[10px] font-black uppercase tracking-wider">Pronto</span>
                            </span>
                        <?php else: ?>
                            <a href="<?= e(url($item['path'])) ?>" class="inline-flex border-b-2 px-3 py-3 text-sm font-bold transition <?= $active ? 'border-accent text-primary' : 'border-transparent text-slate-600 hover:text-primary' ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                                <?= e($item['label']) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <main id="contenido" class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-10">
        <?php foreach ($flashes as $flash): ?>
            <div class="mb-6 border px-4 py-3 text-sm font-semibold <?= $flash['type'] === 'error' ? 'border-accent/25 bg-accent/10 text-accent' : 'border-secondary/25 bg-secondary/10 text-secondary' ?>" role="status">
                <?= e($flash['message']) ?>
            </div>
        <?php endforeach; ?>

        <?= $content ?>
    </main>
</body>
</html>
