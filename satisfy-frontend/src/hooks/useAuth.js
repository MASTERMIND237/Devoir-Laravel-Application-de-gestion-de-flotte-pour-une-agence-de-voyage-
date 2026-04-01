import { useMutation, useQuery } from '@tanstack/react-query';
import { authApi } from '../api/auth.api';
import { useAuthStore } from '../store/authStore';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';

export const useAuth = () => {
  const navigate = useNavigate();
  const { login: setLogin, logout: setLogout } = useAuthStore();

  const loginMutation = useMutation({
    mutationFn: (credentials) => authApi.login(credentials),
    onSuccess: (response) => {
      console.debug('useAuth login onSuccess response:', response);
      const user = response.user;
      const token = response.token;
      setLogin(user, token);
      toast.success(`Bienvenue, ${user?.nom_complet || user?.name || 'utilisateur'}`);
      navigate('/dashboard');
    },
    onError: (error) => {
      // Log complet pour debug (voir console navigateur)
      console.error('Login error:', error);
      toast.error(error.response?.data?.message || error.message || 'Identifiants invalides');
    },
  });

  const logoutMutation = useMutation({
    mutationFn: authApi.logout,
    onSuccess: () => {
      setLogout();
      navigate('/login');
      toast.success('Déconnexion réussie');
    }
  });

  const registerMutation = useMutation({
    mutationFn: (payload) => authApi.register(payload),
    onSuccess: (response) => {
      console.debug('useAuth register onSuccess response:', response);
      const user = response.user;
      const token = response.token;
      setLogin(user, token);
      toast.success('Inscription réussie. Bienvenue !');
      navigate('/dashboard');
    },
    onError: (error) => {
      console.error('Register error:', error);
      toast.error(error.response?.data?.message || error.message || 'Erreur lors de l\'inscription');
    }
  });

  return {
    login: loginMutation.mutate,
    register: registerMutation.mutate,
    logout: logoutMutation.mutate,
    isLoading:
      loginMutation.isPending || logoutMutation.isPending || registerMutation.isPending,
    user: useAuthStore((state) => state.user),
    isAuthenticated: useAuthStore((state) => state.isAuthenticated),
  };
};