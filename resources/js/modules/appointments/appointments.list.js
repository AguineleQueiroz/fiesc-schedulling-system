import {appointmentApi} from './api.js';
import {notify} from '../../core/notify.js';

const modal = document.getElementById('modal-cancel');
const modalCancel = document.getElementById('modal-cancel-btn');
const modalConfirm = document.getElementById('modal-confirm-btn');

let pendingCancel = null; // { id, row }

function openModal(id, row) {
    pendingCancel = {id, row};
    modal.classList.remove('hidden');
}

function closeModal() {
    pendingCancel = null;
    modal.classList.add('hidden');
}

document.querySelectorAll('[data-action="cancel-appointment"]').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        openModal(btn.dataset.id, row);
    });
});

modalCancel.addEventListener('click', closeModal);
modal.addEventListener('click', e => {
    if (e.target === modal) closeModal();
});

modalConfirm.addEventListener('click', async () => {
    if (!pendingCancel) return;

    modalConfirm.disabled = true;

    try {
        await appointmentApi.cancel(pendingCancel.id);
        // Atualiza o status na linha sem recarregar a página
        const statusCell = pendingCancel.row.querySelector('td:nth-child(6)');
        if (statusCell) {
            statusCell.innerHTML = `<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Cancelado</span>`;
        }
        const actionCell = pendingCancel.row.querySelector('td:last-child');
        if (actionCell) actionCell.innerHTML = '';

        notify.success('Agendamento cancelado. O horário está livre novamente.');
        closeModal();
    } catch (err) {
        notify.error(err.message || 'Erro ao cancelar o agendamento.');
        closeModal();
    } finally {
        modalConfirm.disabled = false;
    }
});
