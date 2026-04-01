import api from './axios';

export const rapportsApi = {
  getStatsGenerales: () => api.get('/rapports/stats'),
  getConsommationCarburant: (params) => api.get('/rapports/carburant', { params }),
  getActiviteVehicule: (id, params) => api.get(`/rapports/vehicule/${id}`, { params }),
  exportPDF: (type) => api.get(`/rapports/export/${type}`, { responseType: 'blob' }),
};