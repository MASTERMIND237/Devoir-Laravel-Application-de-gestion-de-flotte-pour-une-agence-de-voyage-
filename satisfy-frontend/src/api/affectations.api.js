import api from './axios';

export const affectationsApi = {
  getAll: (params) => api.get('/affectations', { params }),
  getById: (id) => api.get(`/affectations/${id}`),
  create: (data) => api.post('/affectations', data),
  update: (id, data) => api.put(`/affectations/${id}`, data),
  delete: (id) => api.delete(`/affectations/${id}`),
  getPlanning: () => api.get('/affectations/planning'), // Pour la vue calendrier/planning
};