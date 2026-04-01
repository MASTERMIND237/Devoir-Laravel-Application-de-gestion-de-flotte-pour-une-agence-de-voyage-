import api from './axios';

export const maintenancesApi = {
  getAll: (params) => api.get('/maintenances', { params }),
  getById: (id) => api.get(`/maintenances/${id}`),
  create: (data) => api.post('/maintenances', data),
  update: (id, data) => api.put(`/maintenances/${id}`, data),
  delete: (id) => api.delete(`/maintenances/${id}`),
  complete: (id) => api.patch(`/maintenances/${id}/terminer`), // Marquer comme effectué
};