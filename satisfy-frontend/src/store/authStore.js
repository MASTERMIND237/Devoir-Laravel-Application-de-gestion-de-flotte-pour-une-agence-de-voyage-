import { create } from 'zustand';

export const useAuthStore = create((set) => ({
  // État initial : on tente de récupérer les infos du localStorage
  user: JSON.parse(localStorage.getItem('user')) || null,
  token: localStorage.getItem('token') || null,
  isAuthenticated: !!localStorage.getItem('token'),

  // Action pour se connecter
  login: (userData, token) => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(userData));
    set({ 
      user: userData, 
      token: token, 
      isAuthenticated: true 
    });
  },

  // Action pour se déconnecter
  logout: () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    set({ 
      user: null, 
      token: null, 
      isAuthenticated: false 
    });
  },

  // Action pour mettre à jour les infos utilisateur (ex: profil)
  updateUser: (updatedData) => {
    const newUser = { ...JSON.parse(localStorage.getItem('user')), ...updatedData };
    localStorage.setItem('user', JSON.stringify(newUser));
    set({ user: newUser });
  }
}));