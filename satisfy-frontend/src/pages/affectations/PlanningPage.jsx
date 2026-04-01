import React from 'react';
import { Card } from '../../components/ui/Card';
import { PageHeader } from '../../components/layout/PageHeader';
import { ChevronLeft, ChevronRight } from 'lucide-react';

const PlanningPage = () => {
  const jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

  return (
    <div className="space-y-6">
      <PageHeader title="Planning Hebdomadaire" subtitle="Visualisation de la disponibilité de la flotte." />
      
      <Card>
        <div className="flex items-center justify-between mb-8">
          <div className="flex gap-2">
            <button className="p-2 hover:bg-sand rounded-lg"><ChevronLeft size={20}/></button>
            <h3 className="text-lg font-bold text-cyprus self-center">30 Mars — 05 Avril 2026</h3>
            <button className="p-2 hover:bg-sand rounded-lg"><ChevronRight size={20}/></button>
          </div>
          <div className="flex gap-4 text-xs font-bold uppercase">
            <span className="flex items-center gap-2"><div className="w-3 h-3 bg-kiwi rounded-full"/> Mission</span>
            <span className="flex items-center gap-2"><div className="w-3 h-3 bg-orange-400 rounded-full"/> Maintenance</span>
          </div>
        </div>

        <div className="overflow-x-auto">
          <div className="min-w-[800px]">
            <div className="grid grid-cols-8 border-b border-sand-dark pb-4">
              <div className="text-xs font-bold text-cyprus/40 uppercase">Véhicule</div>
              {jours.map(j => <div key={j} className="text-center text-xs font-bold text-cyprus uppercase">{j}</div>)}
            </div>

            {/* Exemple de ligne de planning */}
            <div className="grid grid-cols-8 py-4 border-b border-sand-light items-center">
              <div className="text-sm font-bold text-cyprus">LT-882-CI</div>
              <div className="col-span-4 px-2">
                <div className="bg-kiwi text-cyprus-dark text-[10px] font-bold p-2 rounded-lg shadow-sm truncate">
                  Mission: Douala - Yaoundé (J. Dupont)
                </div>
              </div>
              <div className="col-span-3 bg-sand-dark/20 h-8 rounded-lg mx-2 border border-dashed border-sand-dark" />
            </div>
          </div>
        </div>
      </Card>
    </div>
  );
};

export default PlanningPage;