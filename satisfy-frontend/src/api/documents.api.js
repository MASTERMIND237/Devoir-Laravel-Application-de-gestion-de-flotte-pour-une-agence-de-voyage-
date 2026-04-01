import api from './axios';

export const documentsApi = {
  getAll: (params) => api.get('/documents', { params }),
  upload: (formData) => api.post('/documents/upload', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  download: (id) => api.get(`/documents/download/${id}`, { responseType: 'blob' }),
  delete: (id) => api.delete(`/documents/${id}`),
};