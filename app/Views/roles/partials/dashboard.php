<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$assetPath = ($basePath === '' || $basePath === '.') ? '/assets' : $basePath . '/assets';
$logoutPath = \App\Support\Auth::route('/logout');
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004E64">
    <title><?= $escape($data['title']) ?> · Clínica</title>
    <link rel="icon" type="image/png" href="<?= $escape($assetPath . '/logos/Dientes_logo_blanco.png') ?>">
    <link rel="stylesheet" href="<?= $escape($assetPath . '/css/app.css') ?>">
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <header class="border-b border-primary/10 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-4 sm:px-8">
            <a href="<?= $escape(\App\Support\Auth::route('/')) ?>" aria-label="Ir al inicio">
                <img src="<?= $escape($assetPath . '/logos/logo_letras.png') ?>" alt="Clínica" class="h-9 w-auto max-w-[180px] object-contain">
            </a>
            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-bold text-primary"><?= $escape((string) $user['nombre']) ?></p>
                    <p class="text-xs text-slate-500"><?= $escape((string) $user['correo']) ?></p>
                </div>
                <a href="<?= $escape($logoutPath) ?>" class="border border-primary/15 px-4 py-2 text-sm font-bold text-primary transition hover:border-accent hover:text-accent">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8 sm:py-14">
        <section class="border border-primary/10 bg-primary px-7 py-9 text-white shadow-xl shadow-primary/10 sm:px-10">
            <div class="flex flex-col justify-between gap-8 sm:flex-row sm:items-end">
                <div>
                    <span class="inline-flex border border-white/25 px-3 py-1 text-xs font-black uppercase tracking-[0.16em] text-white/80">Sesión activa · <?= $escape($data['role']) ?></span>
                    <h1 class="mt-5 max-w-2xl text-3xl font-black tracking-tight sm:text-5xl"><?= $escape($data['title']) ?></h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-white/75"><?= $escape($data['message']) ?></p>
                </div>
                <div class="hidden h-20 w-20 shrink-0 items-center justify-center border border-white/20 bg-white/10 sm:flex">
                    <img src="<?= $escape($assetPath . '/logos/Dientes_logo_blanco.png') ?>" alt="" class="h-12 w-12 object-contain">
                </div>
            </div>
        </section>

        <section class="mt-8">
            <div class="flex items-end justify-between gap-5">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">Accesos rápidos</p>
                    <h2 class="mt-2 text-2xl font-black text-primary">Tu espacio de trabajo</h2>
                </div>
                <span class="hidden text-sm text-slate-500 sm:block">Vista provisional</span>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <?php foreach ($data['actions'] as $index => $action): ?>
                    <article class="border border-primary/10 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/10">
                        <span class="text-sm font-black text-accent">0<?= $index + 1 ?></span>
                        <h3 class="mt-8 text-xl font-black text-primary"><?= $escape($action['title']) ?></h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600"><?= $escape($action['description']) ?></p>
                        <span class="mt-6 inline-block text-xs font-black uppercase tracking-[0.14em] text-slate-400">Próximamente</span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
