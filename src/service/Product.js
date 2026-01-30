import api from './Api';

export const ProductService = {

    // GET /products
    list(params = {}) {
        return api.get('/products/list', { params });
    },

    // GET /products/{id}
    show(id) {
        return api.get(`/products/show/${id}`);
    },

    // POST /products
    create(payload) {
        const headers = payload instanceof FormData
            ? { 'Content-Type': 'multipart/form-data' }
            : {};

        return api.post('/products/store', payload, { headers });
    },

    // POST /products/{id}
    update(id, payload) {
        const headers = payload instanceof FormData
            ? { 'Content-Type': 'multipart/form-data' }
            : {};

        if (payload instanceof FormData) {
            payload.append('_method', 'POST');
            return api.post(`/products/update/${id}`, payload, { headers });
        }

        return api.post(`/products/update/${id}`, payload, { headers });
    },

    // DELETE /products/{id}
    delete(id) {
        return api.delete(`/products/delete/${id}`);
    }
};
