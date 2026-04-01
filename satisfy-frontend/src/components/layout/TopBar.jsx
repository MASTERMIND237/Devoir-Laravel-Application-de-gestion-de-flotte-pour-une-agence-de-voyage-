import React from 'react';
import { Bell, Search, UserCircle } from 'lucide-react';
import { useAuthStore } from '../../store/authStore';

export const TopBar = () => {
  const user = useAuthStore((state) => state.user);

  return (
    <header className="h-20 flex items-center justify-between px-8 bg-transparent">
      {/* Recherche simple */}
      <div className="relative w-96">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-cyprus/40" size={18} />
        <input 
          type="text" 
          placeholder="Rechercher un véhicule, un chauffeur..." 
          className="w-full bg-white border border-sand-dark rounded-xl py-2.5 pl-10 pr-4 outline-none focus:border-cyprus transition-colors text-sm"
        />
      </div>

      {/* Profil & Notifs */}
      <div className="flex items-center gap-6">
        <button className="relative p-2 text-cyprus hover:bg-white rounded-lg transition-colors">
          <Bell size={22} />
          <span className="absolute top-2 right-2 w-2 h-2 bg-kiwi rounded-full border-2 border-sand" />
        </button>
        
        <div className="flex items-center gap-3 pl-6 border-l border-sand-dark">
          <div className="text-right">
            <p className="text-sm font-bold text-cyprus leading-none">{user?.name || 'Administrateur'}</p>
            <p className="text-xs text-cyprus/60 mt-1">{user?.role || 'Gestion Fleet'}</p>
          </div>
          <div className="w-10 h-10 bg-cyprus text-kiwi rounded-full flex items-center justify-center font-bold border-2 border-white shadow-sm">
            {user?.name?.charAt(0) || 'A'}
          </div>
        </div>
      </div>
    </header>
  );
};