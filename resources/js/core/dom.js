export function clearErrors(form) {
    form.querySelectorAll('[data-error-for]').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });
}

export function showErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const el = form.querySelector(`[data-error-for="${field}"]`);
        if (el) {
            el.textContent = Array.isArray(messages) ? messages[0] : messages;
            el.classList.remove('hidden');
        }
    });
}

export function formData(form) {
    const data = {};
    new FormData(form).forEach((value, key) => {
        if (key !== '_token') data[key] = value;
    });
    return data;
}
