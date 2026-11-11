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
