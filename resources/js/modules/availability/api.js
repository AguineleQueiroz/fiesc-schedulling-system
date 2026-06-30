import {http} from '../../core/http.js';

export const availabilityApi = {
    byAttendant: (id) => http.get(`/availabilities/attendant/${id}`),
    store: (data) => http.post('/availabilities', data),
    update: (id, data) => http.put(`/availabilities/${id}`, data),
    destroy: (id) => http.delete(`/availabilities/${id}`),
};
