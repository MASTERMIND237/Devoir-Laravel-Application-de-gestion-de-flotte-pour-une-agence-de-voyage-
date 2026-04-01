import api from './axios';

// Normalise les réponses d'auth pour toujours exposer { user, token }
export const authApi = {
  login: (credentials) =>
    api.post('/auth/login', credentials).then((res) => {
      const payload = {
        user: res.data?.data || res.data?.user || res.data,
        token: res.data?.token,
        raw: res,
      };
      console.debug('authApi.login -> normalized payload:', payload);
      return payload;
    }),

  register: (payload) =>
    api.post('/auth/register', payload).then((res) => {
      const p = {
        user: res.data?.data || res.data?.user || res.data,
        token: res.data?.token,
        raw: res,
      };
      console.debug('authApi.register -> normalized payload:', p);
      return p;
    }),

  logout: () => api.post('/auth/logout').then((res) => res.data),

  getMe: () =>
    api.get('/auth/me').then((res) => {
      const p = { user: res.data?.data || res.data?.user || res.data, raw: res };
      console.debug('authApi.getMe -> normalized payload:', p);
      return p;
    }),
};