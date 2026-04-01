import api from './axios';

export const vehiculesApi = {
  getAll: (params) => api.get('/vehicules', { params }),
  getById: (id) => api.get(`/vehicules/${id}`),
  create: (data) => api.post('/vehicules', data),
  update: (id, data) => api.put(`/vehicules/${id}`, data),
  delete: (id) => api.delete(`/vehicules/${id}`),
  getMapPositions: () => api.get('/vehicules/carte'),
};