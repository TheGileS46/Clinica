// RNF03.5: confirmación antes de acciones destructivas (cancelar, desactivar).
// Uso: <form data-confirm="¿Seguro?"> o <button data-confirm="¿Seguro?">.
document.addEventListener('submit', (event) => {
    const form = event.target;
    const submitter = event.submitter;
    const message = (submitter && submitter.dataset.confirm) || form.dataset.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

// RF05.4: horarios libres del médico. Se recargan al cambiar médico, fecha o duración.
// Cada horario es un botón de envío, así una cita se agenda en ≤ 4 interacciones (RNF03.2).
(() => {
    const form = document.querySelector('form[data-slots]');

    if (!form) {
        return;
    }

    const list = form.querySelector('[data-slot-list]');
    const status = form.querySelector('[data-slot-status]');
    const refresh = form.querySelector('[data-slot-refresh]');
    const field = (name) => form.elements.namedItem(name);
    const actionLabel = list.dataset.actionLabel || 'Agendar';
    let controller = null;

    if (refresh) {
        refresh.hidden = true;
    }

    const render = (slots, fecha) => {
        list.replaceChildren(...slots.map((slot) => {
            const button = document.createElement('button');
            button.type = 'submit';
            button.name = 'hora';
            button.value = slot;
            button.className = 'btn-outline py-2.5';
            button.textContent = slot;
            button.setAttribute('aria-label', `${actionLabel} a las ${slot}`);
            return button;
        }));

        const [y, m, d] = fecha.split('-');
        status.textContent = slots.length
            ? `${slots.length} horario(s) libre(s) el ${d}/${m}/${y}. Haz clic para ${actionLabel.toLowerCase()}.`
            : 'No hay horarios libres ese día. Prueba con otra fecha.';
    };

    const load = async () => {
        const medico = field('medico_id').value;
        const fecha = field('fecha').value;

        if (!medico || !fecha) {
            list.replaceChildren();
            status.textContent = 'Selecciona un médico y una fecha para ver sus horarios.';
            return;
        }

        controller?.abort();
        controller = new AbortController();
        status.textContent = 'Buscando horarios…';

        const params = new URLSearchParams({
            medico_id: medico,
            fecha,
            duracion: field('duracion').value,
            except: form.dataset.except || '0',
        });

        try {
            const response = await fetch(`${form.dataset.slots}?${params}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error(String(response.status));
            }

            render((await response.json()).slots || [], fecha);
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'No fue posible consultar los horarios. Recarga la página.';
            }
        }
    };

    form.querySelectorAll('[data-slot-input]').forEach((input) => input.addEventListener('change', load));
})();
