import {appointmentApi} from './api.js';
import {notify} from '../../core/notify.js';
import {clearErrors, formData, showErrors} from '../../core/dom.js';

const form = document.getElementById('schedule-form');
const attendantEl = document.getElementById('attendant_id');
const dateEl = document.getElementById('date');
const slotSection = document.getElementById('slots-section');
const slotSelect = document.getElementById('slot');
const noSlotsMsg = document.getElementById('no-slots-msg');
const startEl = document.getElementById('start_time');
const endEl = document.getElementById('end_time');

async function fetchSlots() {
    const attendantId = attendantEl.value;
    const date = dateEl.value;
    if (!attendantId || !date) return;

    slotSelect.innerHTML = '<option value="">Carregando...</option>';
    slotSection.style.display = 'block';
    noSlotsMsg.classList.add('hidden');

    try {
        const slots = await appointmentApi.slots(attendantId, date);
        slotSelect.innerHTML = '<option value="">Selecione um horário...</option>';

        if (slots.length === 0) {
            noSlotsMsg.classList.remove('hidden');
        } else {
            slots.forEach(s => {
                const opt = document.createElement('option');
                opt.value = JSON.stringify(s);
                opt.textContent = s.label;
                slotSelect.appendChild(opt);
            });
        }
    } catch {
        notify.error('Erro ao carregar horários disponíveis.');
        slotSection.style.display = 'none';
    }
}

attendantEl.addEventListener('change', fetchSlots);
dateEl.addEventListener('change', fetchSlots);

slotSelect.addEventListener('change', () => {
    if (!slotSelect.value) {
        startEl.value = '';
        endEl.value = '';
        return;
    }
    const slot = JSON.parse(slotSelect.value);
    startEl.value = slot.start;
    endEl.value = slot.end;
});

form.addEventListener('submit', async e => {
    e.preventDefault();
    clearErrors(form);

    const data = formData(form);
    // Remove o campo 'slot' (é virtual), garante start/end_time vindos dos hidden
    delete data.slot;

    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Agendando...';

    try {
        await appointmentApi.store(data);
        window.location.href = form.dataset.redirect;
    } catch (err) {
        if (err.status === 422 && Object.keys(err.errors ?? {}).length > 0) {
            showErrors(form, err.errors);
            notify.error('Verifique os campos destacados em vermelho.');
        } else {
            notify.error(err.message || 'Erro ao criar agendamento.');
        }
        btn.disabled = false;
        btn.textContent = 'Confirmar Agendamento';
    }
});
