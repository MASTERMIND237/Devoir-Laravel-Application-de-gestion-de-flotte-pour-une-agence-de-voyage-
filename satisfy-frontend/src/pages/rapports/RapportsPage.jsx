import React from 'react';
import { PageHeader } from '../../components/layout/PageHeader';
import { Card } from '../../components/ui/Card';
import { KmChart } from '../../components/charts/KmChart';
import { DescriptionItem } from '../../components/ui/DescriptionList';
import { formatters } from '../../utils/formatters';

const RapportsPage = () => {
  return (
    <div className="space-y-8">
      <PageHeader title="Rapports d'Activité" subtitle="Analyse mensuelle de la performance de la flotte." />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Colonne de Gauche : Graphique Principal */}
        <div className="lg:col-span-2 space-y-6">
          <Card title="Évolution de la Distance (KM)">
            <KmChart data={[]} /> {/* Utilise les données réelles ici */}
          </Card>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card title="Top Chauffeurs (Distance)">
              <DescriptionItem label="Jean Dupont" value="2,450 km" subValue="12 Missions" />
              <DescriptionItem label="Marie Fouda" value="1,980 km" subValue="9 Missions" />
              <DescriptionItem label="Alain Boma" value="1,200 km" subValue="5 Missions" />
            </Card>
            
            <Card title="Consommation par Véhicule">
              <DescriptionItem label="LT-882-CI (Hilux)" value="8.5 L/100" subValue="Optimal" />
              <DescriptionItem label="CE-441-AF (Prado)" value="12.2 L/100" subValue="Élevé" />
              <DescriptionItem label="EN-102-RE (Canter)" value="15.0 L/100" subValue="Check Moteur requis" />
            </Card>
          </div>
        </div>

        {/* Colonne de Droite : Résumé des Coûts */}
        <div className="space-y-6">
          <Card title="Récapitulatif Financier" className="bg-cyprus text-white border-none">
            <div className="space-y-4">
              <div>
                <p className="text-xs opacity-60 uppercase font-bold">Carburant</p>
                <p className="text-2xl font-syne font-bold">{formatters.currency(850000)}</p>
              </div>
              <div>
                <p className="text-xs opacity-60 uppercase font-bold">Maintenances</p>
                <p className="text-2xl font-syne font-bold text-kiwi">{formatters.currency(120000)}</p>
              </div>
            </div>
          </Card>
          
          <Card title="Alertes de Performance">
             <div className="p-3 bg-red-50 rounded-xl border border-red-100 text-red-700 text-sm">
                ⚠️ <strong>Alerte Chauffeur :</strong> Alain Boma a dépassé la limite de vitesse 3 fois cette semaine.
             </div>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default RapportsPage;