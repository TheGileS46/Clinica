<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$assetPath = ($basePath === '' || $basePath === '.') ? '/assets' : $basePath . '/assets';
$logoPath = $assetPath . '/logos';
$iconPath = $assetPath . '/icons';
$whatsappUrl = 'https://wa.me/' . $data['whatsapp'] . '?text=' . rawurlencode('Hola, quiero solicitar información sobre una cita.');
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($data['address']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Conoce los servicios de Clínica y solicita información o una cita por WhatsApp en Tonalá.">
    <meta name="theme-color" content="#004E64">
    <title><?= htmlspecialchars($data['brand'], ENT_QUOTES, 'UTF-8') ?> · Atención médica</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($logoPath . '/Dientes_logo_blanco.png', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($logoPath . '/Dientes_logo_blanco.png', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetPath . '/css/app.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="bg-background text-text antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-primary focus:px-4 focus:py-3 focus:text-white">
        Saltar al contenido
    </a>

    <header class="border-b border-primary/10 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
            <a href="#inicio" class="flex items-center gap-3" aria-label="Ir al inicio">
                <img src="<?= htmlspecialchars($logoPath . '/logo_letras.png', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($data['brand'], ENT_QUOTES, 'UTF-8') ?>" class="h-9 w-auto max-w-[170px] object-contain">
            </a>

            <nav class="hidden items-center gap-8 text-sm font-semibold text-slate-600 md:flex" aria-label="Navegación principal">
                <a class="transition hover:text-primary" href="#servicios">Servicios</a>
                <a class="transition hover:text-primary" href="#especialistas">Especialistas</a>
                <a class="transition hover:text-primary" href="#contacto">Ubicación y contacto</a>
            </nav>

            <a href="login" class="inline-flex items-center rounded-lg border border-primary px-4 py-2.5 text-sm font-bold text-primary transition hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                Acceso médicos
            </a>
        </div>
    </header>

    <main id="contenido">
        <section id="inicio" class="border-b border-primary/10 bg-background">
            <div class="mx-auto grid max-w-7xl gap-14 px-5 py-20 sm:px-8 sm:py-24 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-28">
                <div>
                    <p class="inline-flex items-center gap-2 text-sm font-bold text-secondary">
                        <span class="h-2.5 w-2.5 rounded-full bg-secondary" aria-hidden="true"></span>
                        Atención médica en Tonalá
                    </p>
                    <h1 class="mt-6 max-w-3xl text-5xl font-black tracking-tight text-primary sm:text-6xl">
                        <?= htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl">
                        <?= htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-accent px-5 py-3.5 font-bold text-white transition hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                            <img src="<?= htmlspecialchars($iconPath . '/whatsapp.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="mr-2 h-5 w-5">
                            Solicitar cita por WhatsApp
                            <span class="ml-2 text-lg" aria-hidden="true">↗</span>
                        </a>
                        <a href="#servicios" class="inline-flex items-center justify-center rounded-lg border border-primary/20 bg-white px-5 py-3.5 font-bold text-primary transition hover:border-primary hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            Ver servicios
                        </a>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">La disponibilidad y los horarios se confirman directamente con la clínica.</p>
                </div>

                <div class="relative overflow-hidden border-l-4 border-secondary bg-primary px-7 py-8 text-white shadow-sm sm:px-9">
                    <img src="<?= htmlspecialchars($assetPath . '/images/Medicos_ayudando.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="absolute inset-0 h-full w-full object-cover opacity-20">
                    <div class="relative z-10">
                    <div class="flex items-start justify-between gap-5">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.16em] text-white/70">Agenda de forma sencilla</p>
                            <h2 class="mt-4 text-3xl font-black tracking-tight">¿Necesitas orientación?</h2>
                        </div>
                        <img src="<?= htmlspecialchars($logoPath . '/Dientes_logo_blanco.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-14 w-14 object-contain">
                    </div>
                    <p class="mt-4 leading-7 text-white/75">Escríbenos y te ayudaremos a conocer el siguiente paso antes de acudir a la clínica.</p>
                    <ol class="mt-8 space-y-6">
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white">1</span>
                            <div><p class="font-bold">Cuéntanos qué necesitas</p><p class="mt-1 text-sm leading-6 text-white/65">Envíanos un mensaje por WhatsApp.</p></div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white">2</span>
                            <div><p class="font-bold">Confirmamos la información</p><p class="mt-1 text-sm leading-6 text-white/65">Te compartimos disponibilidad y requisitos vigentes.</p></div>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white">3</span>
                            <div><p class="font-bold">Recibe tu confirmación</p><p class="mt-1 text-sm leading-6 text-white/65">La cita queda confirmada por el mismo canal.</p></div>
                        </li>
                    </ol>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicios" class="border-b border-primary/10 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:py-24">
                <div class="max-w-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">Servicios</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-primary sm:text-4xl">Información para tomar el siguiente paso.</h2>
                    <p class="mt-4 leading-7 text-slate-600">Te orientamos sobre la atención disponible y confirmamos los detalles antes de agendar.</p>
                </div>
                <div class="mt-12 grid gap-5 md:grid-cols-3">
                    <?php foreach ($data['services'] as $service): ?>
                        <?php $sceneImage = in_array($service['image'], ['Medicos_ayudando.png', 'recepcionista.png'], true); ?>
                        <article class="relative mt-14 border border-primary/10 bg-background p-7 pt-24 transition hover:border-secondary/40 hover:shadow-md">
                            <div class="absolute -top-14 right-5 z-10 <?= $sceneImage ? 'h-28 w-40 overflow-hidden rounded-xl border-4 border-background shadow-lg' : 'h-36 w-36' ?>">
                                <img src="<?= htmlspecialchars($assetPath . '/images/' . $service['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-full w-full <?= $sceneImage ? 'object-cover' : 'object-contain drop-shadow-xl' ?>">
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-secondary/10" aria-hidden="true">
                                <?php $serviceIcon = ['01' => 'clipboard.svg', '02' => 'clock.svg', '03' => 'message.svg'][$service['number']] ?? 'message.svg'; ?>
                                <img src="<?= htmlspecialchars($iconPath . '/' . $serviceIcon, ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5">
                            </div>
                            <p class="mt-7 text-xs font-black uppercase tracking-[0.16em] text-accent"><?= htmlspecialchars($service['number'], ENT_QUOTES, 'UTF-8') ?></p>
                            <h3 class="mt-2 text-xl font-black text-primary"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="mt-3 leading-7 text-slate-600"><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center font-bold text-secondary transition hover:text-accent">Solicitar información <span class="ml-2" aria-hidden="true">→</span></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="especialistas" class="bg-background">
            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:py-24">
                <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <p class="flex items-center gap-2 text-sm font-black uppercase tracking-[0.16em] text-accent">
                            <img src="<?= htmlspecialchars($logoPath . '/Dientes_logo_negro.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-7 w-7 object-contain">
                            Especialistas
                        </p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-primary sm:text-4xl">Profesionales y áreas de atención.</h2>
                        <p class="mt-4 leading-7 text-slate-600">Estamos confirmando los datos de cada consulta para publicar información clara, vigente y aprobada.</p>
                    </div>
                    <p class="max-w-xs text-sm leading-6 text-slate-500">Los horarios se publicarán únicamente cuando la clínica confirme que siguen vigentes.</p>
                </div>

                <div class="mt-12">
                    <?php if ($data['specialists'] !== []): ?>
                        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                            <?php foreach ($data['specialists'] as $specialist): ?>
                                <?php $sceneImage = in_array($specialist['image'], ['Medicos_ayudando.png', 'recepcionista.png'], true); ?>
                                <article class="relative mt-14 border border-primary/10 bg-white p-7 pt-24 shadow-sm">
                                    <div class="absolute -top-14 right-5 z-10 <?= $sceneImage ? 'h-28 w-40 overflow-hidden rounded-xl border-4 border-background shadow-lg' : 'h-36 w-36' ?>">
                                        <img src="<?= htmlspecialchars($assetPath . '/images/' . $specialist['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-full w-full <?= $sceneImage ? 'object-cover' : 'object-contain drop-shadow-xl' ?>">
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-bold text-secondary"><?= htmlspecialchars($specialist['area'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <span class="rounded-full bg-accent/10 px-2.5 py-1 text-[11px] font-bold text-accent">Por confirmar</span>
                                    </div>
                                    <h3 class="mt-5 text-xl font-black text-primary"><?= htmlspecialchars($specialist['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <?php if ($specialist['organization'] !== ''): ?>
                                        <p class="mt-1 text-sm font-semibold text-slate-500"><?= htmlspecialchars($specialist['organization'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                    <p class="mt-5 border-t border-primary/10 pt-4 text-sm leading-6 text-slate-500"><?= htmlspecialchars($specialist['status'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-3 text-sm font-bold text-white">
                                        <img src="<?= htmlspecialchars($iconPath . '/whatsapp.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-4 w-4">
                                        Consultar disponibilidad
                                    </a>
                                </article>
                            <?php endforeach; ?>
                            <?php foreach ($data['pendingAreas'] as $pendingArea): ?>
                                <article class="relative mt-14 border border-dashed border-secondary/35 bg-white p-7 pt-24">
                                    <div class="absolute -top-14 right-5 z-10 h-36 w-36">
                                        <img src="<?= htmlspecialchars($assetPath . '/images/' . $pendingArea['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-full w-full object-contain drop-shadow-xl">
                                    </div>
                                    <p class="text-sm font-bold text-secondary">Área en actualización</p>
                                    <h3 class="mt-3 text-xl font-black text-primary"><?= htmlspecialchars($pendingArea['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="mt-5 border-t border-primary/10 pt-4 text-sm leading-6 text-slate-500"><?= htmlspecialchars($pendingArea['status'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center gap-2 font-bold text-secondary hover:text-accent">Consultar información <span aria-hidden="true">→</span></a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border border-dashed border-secondary/35 bg-white px-6 py-12 text-center sm:px-12">
                            <img src="<?= htmlspecialchars($logoPath . '/Dientes_logo_negro.png', ENT_QUOTES, 'UTF-8') ?>" alt="" class="mx-auto h-12 w-12 object-contain">
                            <h3 class="mt-5 text-xl font-black text-primary">Perfiles en preparación</h3>
                            <p class="mx-auto mt-3 max-w-xl leading-7 text-slate-600">Estamos reuniendo la información aprobada del equipo para ofrecerte perfiles claros y actualizados.</p>
                            <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center font-bold text-secondary hover:text-accent">Solicitar información sobre especialistas <span class="ml-2" aria-hidden="true">→</span></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="contacto" class="border-y border-primary/10 bg-white">
            <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[1fr_0.8fr] lg:items-center lg:py-24">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">Ubicación y contacto</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-primary sm:text-4xl">Estamos en Tonalá.</h2>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">Si necesitas orientación o quieres solicitar una cita, escríbenos por WhatsApp antes de acudir.</p>
                    <div class="mt-8 space-y-6">
                        <div class="flex gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10" aria-hidden="true">
                                <img src="<?= htmlspecialchars($iconPath . '/map-pin.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5">
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-500">Dirección</p>
                                <address class="mt-1 not-italic font-bold text-primary"><?= htmlspecialchars($data['address'], ENT_QUOTES, 'UTF-8') ?></address>
                                <a href="<?= htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-sm font-bold text-secondary underline decoration-secondary/30 underline-offset-4 hover:text-accent">Abrir en Google Maps ↗</a>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10" aria-hidden="true">
                                <img src="<?= htmlspecialchars($iconPath . '/phone.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5">
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-500">WhatsApp</p>
                                <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex items-center gap-2 font-bold text-primary hover:text-accent"><img src="<?= htmlspecialchars($iconPath . '/whatsapp.svg', ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5">+52 <?= htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8') ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-primary/10 bg-background p-8 sm:p-10">
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">¿Tienes una pregunta?</p>
                    <h3 class="mt-3 text-2xl font-black text-primary">Conversemos por WhatsApp.</h3>
                    <p class="mt-3 leading-7 text-slate-600">Nuestro equipo puede orientarte sobre el proceso de atención y confirmar la información disponible.</p>
                    <a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-7 inline-flex w-full items-center justify-center rounded-lg bg-accent px-5 py-3.5 font-bold text-white transition hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">Abrir WhatsApp ↗</a>
                    <div class="relative mt-8 h-28 overflow-hidden border border-primary/10 bg-primary">
                        <img src="<?= htmlspecialchars($assetPath . '/images/recepcionista.png', ENT_QUOTES, 'UTF-8') ?>" alt="Recepción de la clínica" class="h-full w-full object-cover object-center opacity-90">
                    </div>
                </div>
            </div>
        </section>

        <section id="acceso-medicos" class="bg-secondary/10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-14 sm:px-8 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.16em] text-secondary">Área privada</p>
                    <h2 class="mt-3 text-2xl font-black text-primary">Acceso para médicos</h2>
                    <p class="mt-3 leading-7 text-slate-600">El ingreso al sistema clínico estará disponible para personal autorizado. Esta sección queda separada del flujo de pacientes.</p>
                </div>
                <a href="login" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-primary px-5 py-3 font-bold text-white transition hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    Abrir acceso médico
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-primary/10 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-7 text-sm text-slate-500 sm:px-8 md:flex-row md:items-center md:justify-between">
            <p>© <?= date('Y') ?> <?= htmlspecialchars($data['brand'], ENT_QUOTES, 'UTF-8') ?>.</p>
            <p>La información de horarios se confirma antes de agendar.</p>
        </div>
    </footer>
</body>
</html>
