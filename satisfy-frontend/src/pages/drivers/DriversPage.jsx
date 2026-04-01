import React, { useState } from 'react';
import { Plus, Search, User, Phone, MoreVertical, Edit, Trash2, Eye } from 'lucide-react';
import { PageHeader } from '../../components/layout/PageHeader';
import { Button } from '../../components/ui/Button';
import { Table } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';
import { useDrivers } from '../../hooks/useDrivers';
import { Link } from 'react-router-dom';

const DriversPage = () => {
  const { drivers, isLoading, deleteDriver } = useDrivers();
  const [search, setSearch] = useState('');

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Gestion des Chauffeurs" 
        subtitle="Suivez les performances et les affectations de votre équipe."
        action={
          <Link to="/drivers/nouveau">
            <Button className="gap-2">
              <Plus size={18} /> Nouveau Chauffeur
            </Button>
          </Link>
        }
      />

      <div className="bg-white p-4 rounded-2xl border border-sand-dark shadow-sm">
        <div className="relative max-w-md">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-cyprus/40" size={18} />
          <input 
            type="text"
            placeholder="Rechercher un nom ou un téléphone..."
            className="w-full bg-sand-light border-none rounded-xl py-2.5 pl-10 pr-4 outline-none text-sm"
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>
      </div>

      <Table headers={['Chauffeur', 'Contact', 'N° Permis', 'Statut', 'Actions']}>
        {drivers?.map((driver) => (
          <tr key={driver.id} className="hover:bg-sand-light transition-colors">
            <td className="px-6 py-4">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-sand-dark rounded-full flex items-center justify-center text-cyprus font-bold">
                  {driver.nom.charAt(0)}
                </div>
                <div className="flex flex-col">
                  <span className="font-bold text-cyprus">{driver.nom}</span>
                  <span className="text-xs text-cyprus/60">ID: {driver.id}</span>
                </div>
              </div>
            </td>
            <td className="px-6 py-4">
              <div className="flex items-center gap-2 text-sm text-cyprus/70">
                <Phone size={14} /> {driver.telephone}
              </div>
            </td>
            <td className="px-6 py-4 text-sm font-mono">{driver.permis_numero}</td>
            <td className="px-6 py-4">
              <Badge variant={driver.disponible ? 'success' : 'neutral'}>
                {driver.disponible ? 'Disponible' : 'En mission'}
              </Badge>
            </td>
            <td className="px-6 py-4">
              <div className="flex gap-2">
                <Link to={`/drivers/${driver.id}`} className="p-2 hover:bg-cyprus/10 rounded-lg text-cyprus">
                  <Eye size={18} />
                </Link>
                <Link to={`/drivers/edit/${driver.id}`} className="p-2 hover:bg-kiwi/20 rounded-lg text-kiwi-dark">
                  <Edit size={18} />
                </Link>
                <button 
                  onClick={() => deleteDriver(driver.id)}
                  className="p-2 hover:bg-red-100 rounded-lg text-red-500"
                >
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

export default DriversPage;