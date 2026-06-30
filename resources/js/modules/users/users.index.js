import {usersApi} from './api.js';
import {notify} from '../../core/notify.js';

const modal = document.getElementById('modal-delete');
const modalName = document.getElementById('modal-user-name');
const modalCancel = document.getElementById('modal-cancel');
const modalConfirm = document.getElementById('modal-confirm');

let pendingDelete = null; // { id, url, row }

function openModal(id, name, url, row) {
    pendingDelete = {id, url, row};
    modalName.textContent = name;
    modal.classList.remove('hidden');
}

function closeModal() {
    pendingDelete = null;
    modal.classList.add('hidden');
}

document.querySelectorAll('[data-action="delete-user"]').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        openModal(btn.dataset.id, btn.dataset.name, btn.dataset.url, row);
    });
});

modalCancel.addEventListener('click', closeModal);

modal.addEventListener('click', e => {
    if (e.target === modal) closeModal();
});

modalConfirm.addEventListener('click', async () => {
    if (!pendingDelete) return;

    modalConfirm.disabled = true;
    modalConfirm.textContent = 'Excluindo...';

    try {
        await usersApi.destroy(pendingDelete.id);
        pendingDelete.row.remove();
        notify.success('Usuário excluído com sucesso.');
        closeModal();
    } catch (err) {
        notify.error(err.message || 'Erro ao excluir o usuário.');
        closeModal();
    } finally {
        modalConfirm.disabled = false;
        modalConfirm.textContent = 'Excluir';
    }
});
