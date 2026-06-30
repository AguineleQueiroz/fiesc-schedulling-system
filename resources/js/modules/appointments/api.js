import {http} from '../../core/http.js';

export const appointmentApi = {
    slots: (attendantId, date) => http.get('/appointments/available-slots', {attendant_id: attendantId, date}),
    store: (data) => http.post('/appointments', data),
    cancel: (id) => http.delete(`/appointments/${id}`),
};
