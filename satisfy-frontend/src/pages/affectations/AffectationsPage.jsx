import React from 'react';
import { Plus, Calendar, MapPin, Clock, ArrowRight } from 'lucide-react';
import { PageHeader } from '../../components/layout/PageHeader';
import { Button } from '../../components/ui/Button';
import { Table } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';
import { formatters } from '../../utils/formatters';
import { Link } from 'react-router-dom';

const AffectationsPage = () => {
  // Les données proviendraient de useAffectations()
  const affectations = [
    { id: 1, vehicule: 'LT-882-CI', chauffeur: 'Jean Dupont', debut: '2026-03-30', fin: '2026-04-05', statut: 'en_cours' },
    { id: 2, vehicule: 'CE-441-AF', chauffeur: 'Marie Fouda', debut: '2026-03-28', fin: '2026-03-29', statut: 'termine' },
  ];

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Affectations" 
        subtitle="Suivi des attributions chauffeurs-véhicules."
        action={
          <div className="flex gap-3">
            <Link to="/affectations/planning">
              <Button variant="outline" className="gap-2">
                <Calendar size={18} /> Voir le Planning
              </Button>
            </Link>
            <Link to="/affectations/nouveau">
              <Button className="gap-2">
                <Plus size={18} /> Nouvelle Affectation
              </Button>
            </Link>
          </div>
        }
      />

      <Table headers={['Mission', 'Véhicule / Chauffeur', 'Période', 'Statut', 'Actions']}>
        {affectations.map((aff) => (
          <tr key={aff.id} className="hover:bg-sand-light transition-colors">
            <td className="px-6 py-4 font-bold text-cyprus">#AFF-{aff.id}</td>
            <td className="px-6 py-4">
              <div className="flex flex-col">
                <span className="text-sm font-bold text-cyprus">{aff.vehicule}</span>
                <span className="text-xs text-cyprus/60">{aff.chauffeur}</span>
              </div>
            </td>
            <td className="px-6 py-4">
              <div className="flex items-center gap-2 text-xs text-cyprus/70 font-medium">
                {formatters.date(aff.debut)} <ArrowRight size={12}/> {formatters.date(aff.fin)}
              </div>
            </td>
            <td className="px-6 py-4">
              <Badge variant={aff.statut === 'en_cours' ? 'success' : 'neutral'}>
                {aff.statut === 'en_cours' ? 'En cours' : 'Terminé'}
              </Badge>
            </td>
            <td className="px-6 py-4">
               <Button variant="ghost" className="text-xs py-1">Détails</Button>
            </td>
          </tr>
        ))}
      </Table>
    </div>
  );
};

export default AffectationsPage;