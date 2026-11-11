<section class="border border-primary/10 bg-primary px-6 py-8 text-white shadow-xl shadow-primary/10 sm:px-10 sm:py-9">
    <div class="flex flex-col justify-between gap-8 sm:flex-row sm:items-end">
        <div>
            <span class="inline-flex border border-white/25 px-3 py-1 text-xs font-black uppercase tracking-[0.16em] text-white/80">Sesión activa · <?= e($data['role']) ?></span>
            <h1 class="mt-5 max-w-2xl text-3xl font-black tracking-tight sm:text-5xl"><?= e($data['title']) ?></h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/75"><?= e($data['message']) ?></p>
        </div>
        <div class="hidden h-20 w-20 shrink-0 items-center justify-center border border-white/20 bg-white/10 sm:flex">
            <img src="<?= e(asset('logos/Dientes_logo_blanco.png')) ?>" alt="" class="h-12 w-12 object-contain">
        </div>
    </div>
</section>

<section class="mt-8">
    <p class="text-sm font-black uppercase tracking-[0.16em] text-accent">Accesos rápidos</p>
    <h2 class="mt-2 text-2xl font-black text-primary">Tu espacio de trabajo</h2>
    <div class="mt-5 grid gap-4 md:grid-cols-3">
        <?php foreach ($data['actions'] as $index => $action): ?>
            <?php $available = !empty($action['path']); ?>
            <?php if ($available): ?><a href="<?= e(url($action['path'])) ?>" class="block border border-primary/10 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/10 focus:outline-none focus:ring-2 focus:ring-secondary"><?php else: ?><article class="border border-primary/10 bg-white p-6 shadow-sm"><?php endif; ?>
                <span class="text-sm font-black text-accent">0<?= $index + 1 ?></span>
                <h3 class="mt-8 text-xl font-black text-primary"><?= e($action['title']) ?></h3>
                <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($action['description']) ?></p>
                <span class="mt-6 inline-block text-xs font-black uppercase tracking-[0.14em] <?= $available ? 'text-secondary' : 'text-slate-400' ?>"><?= $available ? 'Abrir módulo →' : 'Próximamente' ?></span>
            <?php if ($available): ?></a><?php else: ?></article><?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
