import React from 'react';
import { Outlet } from 'react-router-dom';
import { Sidebar } from './SideBar';
import { TopBar } from './TopBar';

export const AppLayout = () => {
  return (
    <div className="min-h-screen bg-sand font-sans text-cyprus-dark">
      {/* Navigation latérale fixe */}
      <Sidebar />

      {/* Zone de contenu principale (décalée de la largeur de la sidebar) */}
      <main className="ml-64 min-h-screen flex flex-col">
        <TopBar />
        
        {/* Contenu de la page injecté ici */}
        <div className="flex-1 p-8 pt-4">
          <div className="max-w-7xl mx-auto">
            <Outlet />
          </div>
        </div>
      </main>
    </div>
  );
};