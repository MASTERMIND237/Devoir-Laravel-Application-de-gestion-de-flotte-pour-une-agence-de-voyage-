import React from 'react';
import { Card } from '../../components/ui/Card';
import { Table } from '../../components/ui/Table';
import { Badge } from '../../components/ui/Badge';

const RapportDetail = () => {
  return (
    <div className="space-y-6">
      <PageHeader title="Détails du Véhicule : LT-882-CI" subtitle="Historique complet des utilisations et coûts." />

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card title="Utilisation Totale">
          <p className="text-4xl font-syne font-extrabold text-cyprus">452h</p>
          <p className="text-sm text-cyprus/50">Temps de conduite cumulé</p>
        </Card>
        <Card title="Chauffeur Principal">
          <p className="text-2xl font-bold text-cyprus">Jean Dupont</p>
          <p className="text-sm text-cyprus/50">85% des missions</p>
        </Card>
        <Card title="Score Éco-Conduite">
          <p className="text-4xl font-syne font-extrabold text-kiwi">A+</p>
          <p className="text-sm text-cyprus/50">Basé sur le freinage/accélération</p>
        </Card>
      </div>

      <Card title="Historique des conducteurs sur ce véhicule">
        <Table headers={['Chauffeur', 'Période', 'Distance', 'Conso. Réelle']}>
          <tr>
            <td className="px-6 py-4 font-bold text-cyprus">Jean Dupont</td>
            <td className="px-6 py-4 text-sm">01/03 - 15/03</td>
            <td className="px-6 py-4 text-sm">1,200 km</td>
            <td className="px-6 py-4"><Badge variant="success">8.2 L</Badge></td>
          </tr>
          <tr>
            <td className="px-6 py-4 font-bold text-cyprus">Alain Boma</td>
            <td className="px-6 py-4 text-sm">16/03 - 20/03</td>
            <td className="px-6 py-4 text-sm" >450 km</td>
            <td className="px-6 py-4"><Badge variant="danger">10.5 L</Badge></td>
          </tr>
        </Table>
      </Card>
    </div>
  );
};

export default RapportDetail;