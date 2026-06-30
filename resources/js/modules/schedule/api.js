import {http} from '../../core/http.js';

export const scheduleApi = {
    slots: (attendantId, date) => http.get('/schedule/slots', {attendant_id: attendantId, date}),
    store: (data) => http.post('/appointments', data),
    cancel: (id) => http.delete(`/appointments/${id}`),
};
