import { create } from 'zustand';

export const useNotificationStore = create((set) => ({
  notifications: [],
  unreadCount: 0,

  // Ajouter une notification au flux
  addNotification: (notification) => {
    set((state) => ({
      notifications: [
        { id: Date.now(), date: new Date(), read: false, ...notification },
        ...state.notifications
      ].slice(0, 20), // On garde les 20 dernières
      unreadCount: state.unreadCount + 1
    }));
  },

  // Marquer tout comme lu
  markAllAsRead: () => {
    set((state) => ({
      notifications: state.notifications.map(n => ({ ...n, read: true })),
      unreadCount: 0
    }));
  },

  // Supprimer une notification spécifique
  removeNotification: (id) => {
    set((state) => ({
      notifications: state.notifications.filter(n => n.id !== id),
      // Ajuster le compteur si elle n'était pas lue
      unreadCount: state.notifications.find(n => n.id === id)?.read 
        ? state.unreadCount 
        : Math.max(0, state.unreadCount - 1)
    }));
  }
}));