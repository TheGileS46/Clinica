<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$assetPath = ($basePath === '' || $basePath === '.') ? '/assets' : $basePath . '/assets';
$logoPath = $assetPath . '/logos';
$iconPath = $assetPath . '/icons';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004E64">
    <title><?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($data['brand'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($logoPath . '/Dientes_logo_blanco.png', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetPath . '/css/app.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="min-h-screen bg-background text-text antialiased">
    <header class="border-b border-primary/10 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
            <a href="./" class="flex items-center" aria-label="Volver al inicio">
                <img src="<?= htmlspecialchars($logoPath . '/logo_letras.png', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($data['brand'], ENT_QUOTES, 'UTF-8') ?>" class="h-9 w-auto max-w-[180px] object-contain">
            </a>
            <a href="./" class="text-sm font-bold text-primary transition hover:text-secondary">Volver al sitio</a>
        </div>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-74px)] max-w-7xl items-center gap-10 px-5 py-12 sm:px-8 lg:grid-cols-[0.95fr_1.05fr] lg:gap-16 lg:py-20">
        <section class="relative overflow-visible bg-primary px-7 py-10 text-white sm:px-10 sm:py-14 lg:min-h-[590px] lg:px-14 lg:py-16">
            <img src="<?= htmlspecialchars($assetPath . '/images/Medicos_ayudando.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="absolute inset-0 h-full w-full object-cover opacity-15">
            <div class="relative z-10">
                <img src="<?= htmlspecialchars($logoPath . '/Dientes_logo_blanco.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-16 w-16 object-contain">
                <p class="mt-12 text-sm font-bold uppercase tracking-[0.16em] text-white/65">Área privada</p>
                <h1 class="mt-4 max-w-md text-4xl font-black tracking-tight sm:text-5xl">Acceso al sistema clínico.</h1>
                <p class="mt-5 max-w-md text-lg leading-8 text-white/75">Un espacio diseñado para que cada integrante del equipo llegue directamente a las herramientas de su rol.</p>

                <div class="mt-12 space-y-5 border-t border-white/15 pt-8">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-black">01</span>
                        <div><p class="font-bold">Ingresa tus credenciales</p><p class="mt-1 text-sm leading-6 text-white/65">El sistema identificará tu acceso automáticamente.</p></div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-black">02</span>
                        <div><p class="font-bold">Ingresa de forma segura</p><p class="mt-1 text-sm leading-6 text-white/65">Usa las credenciales entregadas por la clínica.</p></div>
                    </div>
                </div>
            </div>
            <img src="<?= htmlspecialchars($assetPath . '/images/conulta_medica.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="pointer-events-none absolute -bottom-16 -right-12 z-20 hidden h-56 w-56 object-contain drop-shadow-2xl lg:block">
        </section>

        <section class="relative z-10 bg-white p-7 shadow-xl shadow-primary/10 sm:p-10 lg:border lg:border-primary/10 lg:p-12">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">Bienvenido</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-primary">Inicia sesión</h2>
                    <p class="mt-3 leading-7 text-slate-600"><?= htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <img src="<?= htmlspecialchars($logoPath . '/Dientes_logo_negro.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-12 w-12 object-contain">
            </div>

            <?php if ($error !== null): ?>
                <div class="mt-6 border border-accent/25 bg-accent/10 px-4 py-3 text-sm font-semibold leading-6 text-accent" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form class="mt-9 space-y-5" action="<?= htmlspecialchars(\App\Support\Auth::route('/login'), ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div>
                    <label for="email" class="text-sm font-bold text-primary">Correo institucional</label>
                    <div class="relative mt-2">
                        <img src="<?= htmlspecialchars($iconPath . '/mail.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2">
                        <input id="email" name="email" type="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" placeholder="nombre@clinica.com" required class="block w-full rounded-lg border border-primary/20 bg-white py-3.5 pl-12 pr-4 text-text outline-none transition placeholder:text-slate-400 focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-4">
                        <label for="password" class="text-sm font-bold text-primary">Contraseña</label>
                        <span class="text-xs font-bold text-slate-500">Solicita ayuda al administrador</span>
                    </div>
                    <div class="relative mt-2">
                        <img src="<?= htmlspecialchars($iconPath . '/lock.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required class="block w-full rounded-lg border border-primary/20 bg-white py-3.5 pl-12 pr-4 text-text outline-none transition placeholder:text-slate-400 focus:border-secondary focus:ring-2 focus:ring-secondary/20">
                    </div>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    Continuar
                    <img src="<?= htmlspecialchars($iconPath . '/arrow-right.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5">
                </button>
            </form>

            <p class="mt-7 border-t border-primary/10 pt-5 text-center text-xs leading-5 text-slate-500">El sistema validará tus credenciales y te dirigirá según el rol registrado en la base de datos.</p>
        </section>
    </main>
</body>
</html>
