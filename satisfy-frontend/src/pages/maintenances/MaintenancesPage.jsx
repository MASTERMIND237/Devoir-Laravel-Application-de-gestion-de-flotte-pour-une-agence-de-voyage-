import React from 'react';
import { Plus, Wrench, AlertCircle, CheckCircle2, Calendar } from 'lucide-react';
import { PageHeader } from '../../components/layout/PageHeader';
import { Button } from '../../components/ui/Button';
import { Table } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';
import { Card } from '../../components/ui/Card';
import { formatters } from '../../utils/formatters';
import { Link } from 'react-router-dom';

const MaintenancesPage = () => {
  // Ces données viendraient de useMaintenances()
  const maintenances = [
    { id: 1, vehicule: 'LT-882-CI', type: 'Vidange', date: '2026-04-05', coût: 45000, statut: 'planifie' },
    { id: 2, vehicule: 'CE-441-AF', type: 'Freins', date: '2026-03-25', coût: 85000, statut: 'en_retard' },
    { id: 3, vehicule: 'EN-102-RE', type: 'Pneus', date: '2026-03-20', coût: 120000, statut: 'termine' },
  ];

  const getStatusBadge = (statut) => {
    switch (statut) {
      case 'planifie': return <Badge variant="info">Planifié</Badge>;
      case 'en_retard': return <Badge variant="danger">En retard</Badge>;
      case 'termine': return <Badge variant="success">Terminé</Badge>;
      default: return <Badge variant="neutral">{statut}</Badge>;
    }
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Entretiens & Maintenances" 
        subtitle="Suivi technique et prévention des pannes."
        action={
          <Link to="/maintenances/nouveau">
            <Button className="gap-2">
              <Plus size={18} /> Programmer un entretien
            </Button>
          </Link>
        }
      />

      {/* Résumé rapide */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white p-4 rounded-2xl border border-sand-dark flex items-center gap-4">
          <div className="p-3 bg-red-100 text-red-600 rounded-xl"><AlertCircle size={24}/></div>
          <div>
            <p className="text-xs font-bold text-cyprus/40 uppercase">Alertes</p>
            <p className="text-xl font-bold text-cyprus">2 En retard</p>
          </div>
        </div>
        <div className="bg-white p-4 rounded-2xl border border-sand-dark flex items-center gap-4">
          <div className="p-3 bg-kiwi/20 text-kiwi-dark rounded-xl"><Calendar size={24}/></div>
          <div>
            <p className="text-xs font-bold text-cyprus/40 uppercase">À venir</p>
            <p className="text-xl font-bold text-cyprus">5 cette semaine</p>
          </div>
        </div>
        <div className="bg-white p-4 rounded-2xl border border-sand-dark flex items-center gap-4">
          <div className="p-3 bg-cyprus/10 text-cyprus rounded-xl"><CheckCircle2 size={24}/></div>
          <div>
            <p className="text-xs font-bold text-cyprus/40 uppercase">Coût total (Mars)</p>
            <p className="text-xl font-bold text-cyprus">{formatters.currency(250000)}</p>
          </div>
        </div>
      </div>

      <Table headers={['Véhicule', 'Type de Service', 'Date Prévue', 'Coût Estimé', 'Statut', 'Actions']}>
        {maintenances.map((m) => (
          <tr key={m.id} className="hover:bg-sand-light transition-colors">
            <td className="px-6 py-4 font-bold text-cyprus">{m.vehicule}</td>
            <td className="px-6 py-4">
              <div className="flex items-center gap-2 text-sm">
                <Wrench size={14} className="text-cyprus/40"/> {m.type}
              </div>
            </td>
            <td className="px-6 py-4 text-sm font-medium">
              {formatters.date(m.date)}
            </td>
            <td className="px-6 py-4 text-sm font-bold">
              {formatters.currency(m.coût)}
            </td>
            <td className="px-6 py-4">
              {getStatusBadge(m.statut)}
            </td>
            <td className="px-6 py-4">
               <button className="text-xs font-bold text-kiwi-dark hover:underline">Marquer fini</button>
            </td>
          </tr>
        ))}
      </Table>
    </div>
  );
};

export default MaintenancesPage;