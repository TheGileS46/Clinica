<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?> · Clínica</title>
    <?php
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $stylesheet = ($basePath === '' || $basePath === '.') ? '/assets/css/app.css' : $basePath . '/assets/css/app.css';
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($stylesheet, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="min-h-screen bg-primary text-white antialiased">
    <main class="mx-auto flex min-h-screen max-w-5xl items-center px-6 py-12">
        <section class="w-full overflow-hidden rounded-3xl border border-white/15 bg-background text-text shadow-2xl shadow-primary/30 backdrop-blur">
            <div class="grid gap-10 px-6 py-10 sm:px-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center lg:px-14 lg:py-14">
                <div>
                    <span class="inline-flex items-center rounded-full border border-secondary/20 bg-secondary/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-secondary">
                        Clínica MVC
                    </span>
                    <h1 class="mt-6 text-4xl font-black tracking-tight text-text sm:text-5xl">
                        <?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                        <?= htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <a class="mt-8 inline-flex items-center rounded-xl bg-accent px-5 py-3 font-bold text-white transition hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-background" href="health">
                        Verificar API
                        <span class="ml-2" aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-lg shadow-primary/10">
                    <p class="text-sm font-medium text-slate-500">Estado del entorno</p>
                    <div class="mt-4 flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full bg-secondary shadow-lg shadow-secondary/50" aria-hidden="true"></span>
                        <span class="font-semibold text-secondary">Aplicación activa</span>
                    </div>
                    <dl class="mt-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-primary/10 pb-3">
                            <dt class="text-slate-500">Entorno</dt>
                            <dd class="font-mono text-primary"><?= htmlspecialchars($data['environment'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">Estilos</dt>
                            <dd class="font-semibold text-text">Tailwind CSS</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
