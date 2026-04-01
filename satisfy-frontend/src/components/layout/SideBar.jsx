import React from 'react';
import { NavLink } from 'react-router-dom';
import { 
  LayoutDashboard, Car, Users, CalendarDays, 
  Wrench, FileBarChart, Settings, LogOut 
} from 'lucide-react';
import { useAuthStore } from '../../store/authStore';

const menuItems = [
  { icon: LayoutDashboard, label: 'Dashboard', path: '/dashboard' },
  { icon: Car, label: 'Véhicules', path: '/vehicules' },
  { icon: Users, label: 'Chauffeurs', path: '/drivers' },
  { icon: CalendarDays, label: 'Affectations', path: '/affectations' },
  { icon: Wrench, label: 'Maintenances', path: '/maintenances' },
  { icon: FileBarChart, label: 'Rapports', path: '/rapports' },
];

export const Sidebar = () => {
  const logout = useAuthStore((state) => state.logout);

  return (
    <aside className="fixed left-0 top-0 h-screen w-64 bg-cyprus text-white flex flex-col z-40">
      {/* Logo Section */}
      <div className="p-8">
        <h1 className="text-3xl font-syne font-extrabold text-kiwi tracking-tighter">
          SATISFY<span className="text-white">.</span>
        </h1>
      </div>

      {/* Navigation */}
      <nav className="flex-1 px-4 space-y-2">
        {menuItems.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            className={({ isActive }) => `
              flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
              ${isActive 
                ? 'bg-kiwi text-cyprus-dark shadow-lg shadow-kiwi/10' 
                : 'text-sand/60 hover:bg-cyprus-mid hover:text-white'}
            `}
          >
            <item.icon size={22} />
            <span className="font-sans">{item.label}</span>
          </NavLink>
        ))}
      </nav>

      {/* Logout */}
      <div className="p-6 border-t border-cyprus-mid">
        <button 
          onClick={logout}
          className="flex items-center gap-3 px-4 py-3 w-full text-sand/60 hover:text-red-400 transition-colors"
        >
          <LogOut size={22} />
          <span className="font-medium">Déconnexion</span>
        </button>
      </div>
    </aside>
  );
};