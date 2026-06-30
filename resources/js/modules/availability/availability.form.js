import {availabilityApi} from './api.js';
import {notify} from '../../core/notify.js';
import {clearErrors, showErrors} from '../../core/dom.js';

const attendantSelect = document.getElementById('attendant-select');
const section = document.getElementById('availability-section');
const formSection = document.getElementById('availability-form-section');
const form = document.getElementById('availability-form');
const body = document.getElementById('availability-body');
const emptyMsg = document.getElementById('availability-empty');
const btnAdd = document.getElementById('btn-add-availability');
const btnCancelForm = document.getElementById('btn-cancel-form');
const formTitle = document.getElementById('form-title');
const availabilityIdEl = document.getElementById('availability-id');

const DAYS = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

let currentAttendantId = null;

function renderRow(av) {
    const tr = document.createElement('tr');
    tr.dataset.avId = av.id;
    tr.innerHTML = `
        <td class="px-4 py-3 text-sm text-gray-900">${DAYS[av.day_of_week]}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${av.start_time.substring(0, 5)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${av.end_time.substring(0, 5)}</td>
        <td class="px-4 py-3 text-sm text-gray-600">${av.active ? 'Sim' : 'Não'}</td>
        <td class="px-4 py-3 text-right space-x-2">
            <button data-action="edit-av" data-id="${av.id}"
                    data-day="${av.day_of_week}" data-start="${av.start_time.substring(0, 5)}"
                    data-end="${av.end_time.substring(0, 5)}" data-active="${av.active ? '1' : '0'}"
                    class="text-blue-600 hover:text-blue-900 text-sm font-medium">Editar</button>
            <button data-action="delete-av" data-id="${av.id}"
                    class="text-red-600 hover:text-red-900 text-sm font-medium">Excluir</button>
        </td>`;
    bindRowActions(tr);
    return tr;
}

function bindRowActions(tr) {
    tr.querySelector('[data-action="edit-av"]')?.addEventListener('click', btn => {
        const d = btn.target.dataset;
        availabilityIdEl.value = d.id;
        document.getElementById('day_of_week').value = d.day;
        document.getElementById('start_time').value = d.start;
        document.getElementById('end_time').value = d.end;
        document.getElementById('active').value = d.active;
        formTitle.textContent = 'Editar Disponibilidade';
        formSection.classList.remove('hidden');
        formSection.scrollIntoView({behavior: 'smooth'});
    });

    tr.querySelector('[data-action="delete-av"]')?.addEventListener('click', async e => {
        const id = e.target.dataset.id;
        if (!confirm('Deseja excluir esta disponibilidade?')) return;
        try {
            await availabilityApi.destroy(id);
            tr.remove();
            notify.success('Disponibilidade removida.');
            checkEmpty();
        } catch (err) {
            notify.error(err.message || 'Erro ao remover.');
        }
    });
}

function checkEmpty() {
    const hasRows = body.querySelectorAll('tr').length > 0;
    emptyMsg.classList.toggle('hidden', hasRows);
}

async function loadAvailabilities(attendantId) {
    body.innerHTML = '';
    try {
        const list = await availabilityApi.byAttendant(attendantId);
        list.forEach(av => body.appendChild(renderRow(av)));
        checkEmpty();
        section.classList.remove('hidden');
    } catch {
        notify.error('Erro ao carregar disponibilidades.');
    }
}

attendantSelect.addEventListener('change', async () => {
    currentAttendantId = attendantSelect.value || null;
    formSection.classList.add('hidden');
    if (currentAttendantId) {
        await loadAvailabilities(currentAttendantId);
    } else {
        section.classList.add('hidden');
    }
});

btnAdd.addEventListener('click', () => {
    form.reset();
    availabilityIdEl.value = '';
    formTitle.textContent = 'Nova Disponibilidade';
    clearErrors(form);
    formSection.classList.remove('hidden');
    formSection.scrollIntoView({behavior: 'smooth'});
});

btnCancelForm.addEventListener('click', () => formSection.classList.add('hidden'));

form.addEventListener('submit', async e => {
    e.preventDefault();
    clearErrors(form);

    const id = availabilityIdEl.value;
    const isEdit = Boolean(id);
    const data = {
        user_id: currentAttendantId,
        day_of_week: document.getElementById('day_of_week').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value,
        active: document.getElementById('active').value === '1',
    };

    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;

    try {
        if (isEdit) {
            const updated = await availabilityApi.update(id, data);
            const existing = body.querySelector(`[data-av-id="${id}"]`);
            existing?.replaceWith(renderRow(updated));
            notify.success('Disponibilidade atualizada.');
        } else {
            const created = await availabilityApi.store(data);
            body.appendChild(renderRow(created));
            checkEmpty();
            notify.success('Disponibilidade cadastrada.');
        }
        formSection.classList.add('hidden');
    } catch (err) {
        if (err.status === 422 && Object.keys(err.errors ?? {}).length > 0) {
            showErrors(form, err.errors);
            notify.error('Verifique os campos destacados em vermelho.');
        } else {
            notify.error(err.message || 'Ocorreu um erro. Tente novamente.');
        }
    } finally {
        btn.disabled = false;
    }
});
