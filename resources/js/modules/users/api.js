import {http} from '../../core/http.js';

export const usersApi = {
    store: (data) => http.post('/users', data),
    update: (id, data) => http.put(`/users/${id}`, data),
    destroy: (id) => http.delete(`/users/${id}`),
};
