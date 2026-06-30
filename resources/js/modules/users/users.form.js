import {usersApi} from './api.js';
import {notify} from '../../core/notify.js';
import {clearErrors, formData, showErrors} from '../../core/dom.js';

const form = document.getElementById('user-form');
const action = form?.dataset.action;   // 'create' | 'edit'
const url = form?.dataset.url;
const redirect = form?.dataset.redirect;

if (form) {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearErrors(form);

        const data = formData(form);
        const btn = form.querySelector('[type="submit"]');
        const label = btn.textContent;

        btn.disabled = true;
        btn.textContent = 'Salvando...';

        try {
            if (action === 'create') {
                await usersApi.store(data);
            } else {
                const id = url.split('/').pop();
                await usersApi.update(id, data);
            }
            window.location.href = redirect;
        } catch (err) {
            if (err.status === 422 && Object.keys(err.errors ?? {}).length > 0) {
                showErrors(form, err.errors);
                notify.error('Verifique os campos destacados em vermelho.');
            } else {
                notify.error(err.message || 'Ocorreu um erro. Tente novamente.');
            }
            btn.disabled = false;
            btn.textContent = label;
        }
    });
}
