import React from 'react';
import { 
  Car, 
  AlertTriangle, 
  TrendingUp, 
  CheckCircle2 
} from 'lucide-react';

// Import des composants UI & Layout
import { PageHeader } from '../../components/layout/PageHeader';
import { Card } from '../../components/ui/Card';
import { Spinner } from '../../components/ui/Spinner';

// Import des composants spécialisés
import { FlotteMap } from '../../components/map/FlotteMap';
import { KmChart } from '../../components/charts/KmChart';
import { MaintenanceChart } from '../../components/charts/MaintenanceChart';

// Import des hooks
import { useVehicules } from '../../hooks/useVehicules';
import { useAuthStore } from '../../store/authStore';

const DashboardPage = () => {
  const { user } = useAuthStore();
  const { mapVehicules, isLoading } = useVehicules();

  // Données factices pour les graphiques (en attendant les données réelles de rapports.api.js)
  const kmData = [
    { name: 'Lun', km: 400 }, { name: 'Mar', km: 300 }, { name: 'Mer', km: 600 },
    { name: 'Jeu', km: 800 }, { name: 'Ven', km: 500 }, { name: 'Sam', km: 900 },
    { name: 'Dim', km: 200 },
  ];

  const maintenanceData = [
    { label: 'Vidange', value: 12 },
    { label: 'Pneus', value: 19 },
    { label: 'Freins', value: 3 },
    { label: 'Moteur', value: 5 },
  ];

  if (isLoading) return <div className="h-full flex items-center justify-center"><Spinner size="lg" /></div>;

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      <PageHeader 
        title={`Bonjour, ${user?.name || 'Gestionnaire'}`} 
        subtitle="Voici l'état actuel de votre flotte pour aujourd'hui."
      />

      {/* 1. SECTION KPI CARDS */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard 
          icon={<Car className="text-kiwi" />} 
          label="Véhicules Actifs" 
          value={mapVehicules.filter(v => v.statut === 'en_route').length} 
          trend="+2 depuis hier"
        />
        <StatCard 
          icon={<AlertTriangle className="text-orange-500" />} 
          label="Maintenances Urgentes" 
          value="3" 
          trend="Action requise"
        />
        <StatCard 
          icon={<TrendingUp className="text-kiwi" />} 
          label="Distance Totale (KM)" 
          value="12,450" 
          trend="+12% ce mois"
        />
        <StatCard 
          icon={<CheckCircle2 className="text-cyprus" />} 
          label="Chauffeurs Libres" 
          value="8" 
          trend="Sur 24 au total"
        />
      </div>

      {/* 2. SECTION CARTE TEMPS RÉEL */}
      <div className="grid grid-cols-1 gap-6">
        <Card title="Suivi Géographique de la Flotte">
          <FlotteMap vehicules={mapVehicules} />
        </Card>
      </div>

      {/* 3. SECTION GRAPHIQUES */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card title="Kilométrage Hebdomadaire">
          <KmChart data={kmData} />
        </Card>
        <Card title="Répartition des Maintenances">
          <MaintenanceChart data={maintenanceData} />
        </Card>
      </div>
    </div>
  );
};

// Petit sous-composant interne pour les stats
const StatCard = ({ icon, label, value, trend }) => (
  <div className="bg-white p-6 rounded-[24px] border border-sand-dark shadow-sm hover:shadow-md transition-shadow">
    <div className="flex items-center gap-4 mb-4">
      <div className="p-3 bg-sand rounded-2xl">{icon}</div>
      <span className="text-cyprus/60 font-medium text-sm">{label}</span>
    </div>
    <div className="flex items-end justify-between">
      <h4 className="text-3xl font-syne font-extrabold text-cyprus">{value}</h4>
      <span className="text-[10px] font-bold uppercase text-cyprus/40 bg-sand px-2 py-1 rounded-md">
        {trend}
      </span>
    </div>
  </div>
);

export default DashboardPage;