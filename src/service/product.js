import api from './api';

export const ProductService = {
    list(params = {}) {
        return api.get('/products', { params });
    },
    show(id) {
        return api.get(`/products/${id}`);
    },
    create(payload) {
        const headers = payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {};
        return api.post('/products', payload, { headers });
    },
    update(id, payload) {
        const headers = payload instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : {};
        if (payload instanceof FormData) {
            payload.append('_method', 'PUT'); // Laravel PUT via POST
            return api.post(`/products/${id}`, payload, { headers });
        }
        return api.put(`/products/${id}`, payload);
    },
    delete(id) {
        return api.delete(`/products/${id}`);
    },
};
