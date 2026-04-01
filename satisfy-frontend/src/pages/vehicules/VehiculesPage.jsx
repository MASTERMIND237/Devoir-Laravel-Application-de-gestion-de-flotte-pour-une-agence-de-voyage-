import React, { useState } from 'react';
import { Plus, Search, Filter, Eye, Edit, Trash2 } from 'lucide-react';
import { PageHeader } from '../../components/layout/PageHeader';
import { Button } from '../../components/ui/Button';
import { Table } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';
import { Input } from '../../components/ui/Input';
import { useVehicules } from '../../hooks/useVehicules';
import { formatters } from '../../utils/formatters';
import { Link } from 'react-router-dom';

const VehiculesPage = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const { vehicules, isLoading } = useVehicules();

  const getStatusVariant = (status) => {
    if (status === 'en_route') return 'success';
    if (status === 'en_maintenance') return 'warning';
    if (status === 'hors_service') return 'danger';
    return 'neutral';
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Flotte de Véhicules" 
        subtitle="Gérez et suivez l'ensemble de vos actifs roulants."
        action={
          <Link to="/vehicules/nouveau">
            <Button variant="primary" className="gap-2">
              <Plus size={18} /> Ajouter un véhicule
            </Button>
          </Link>
        }
      />

      {/* Barre d'outils et Filtres */}
      <div className="flex flex-col md:flex-row gap-4 bg-white p-4 rounded-2xl border border-sand-dark shadow-sm">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-cyprus/40" size={18} />
          <input 
            type="text"
            placeholder="Rechercher par immatriculation ou modèle..."
            className="w-full bg-sand-light border-none rounded-xl py-2.5 pl-10 pr-4 outline-none focus:ring-2 focus:ring-kiwi/50 text-sm"
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <Button variant="outline" className="gap-2 text-sm py-2">
          <Filter size={16} /> Filtres avancés
        </Button>
      </div>

      {/* Tableau des données */}
      <Table headers={['Véhicule', 'Type', 'Statut', 'Consommation', 'Actions']}>
        {vehicules?.map((v) => (
          <tr key={v.id} className="hover:bg-sand-light transition-colors">
            <td className="px-6 py-4">
              <div className="flex flex-col">
                <span className="font-bold text-cyprus">{v.immatriculation}</span>
                <span className="text-xs text-cyprus/60">{v.modele}</span>
              </div>
            </td>
            <td className="px-6 py-4 text-sm capitalize">{v.type}</td>
            <td className="px-6 py-4">
              <Badge variant={getStatusVariant(v.statut)}>
                {formatters.status(v.statut)}
              </Badge>
            </td>
            <td className="px-6 py-4 text-sm font-medium">
              {v.consommation_moyenne} L/100km
            </td>
            <td className="px-6 py-4">
              <div className="flex gap-2">
                <Link to={`/vehicules/${v.id}`} className="p-2 hover:bg-cyprus/10 rounded-lg text-cyprus transition-colors">
                  <Eye size={18} />
                </Link>
                <Link to={`/vehicules/edit/${v.id}`} className="p-2 hover:bg-kiwi/20 rounded-lg text-kiwi-dark transition-colors">
                  <Edit size={18} />
                </Link>
                <button className="p-2 hover:bg-red-100 rounded-lg text-red-500 transition-colors">
                  <Trash2 size={18} />
                </button>
              </div>
            </td>
          </tr>
        ))}
      </Table>
    </div>
  );
};

export default VehiculesPage;