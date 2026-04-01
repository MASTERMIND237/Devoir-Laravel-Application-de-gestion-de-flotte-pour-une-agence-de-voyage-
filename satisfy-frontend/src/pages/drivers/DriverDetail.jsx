import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { ChevronLeft, FileText, Calendar, Award, PhoneCall } from 'lucide-react';
import { Card } from '../../components/ui/Card';
import { Badge } from '../../components/ui/Badge';
import { Button } from '../../components/ui/Button';

const DriverDetail = () => {
  const { id } = useParams();

  return (
    <div className="space-y-6">
      <Link to="/drivers" className="flex items-center gap-2 text-cyprus/60 hover:text-cyprus transition-colors">
        <ChevronLeft size={20} /> Retour
      </Link>

      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div className="flex items-center gap-4">
          <div className="w-20 h-20 bg-cyprus text-kiwi rounded-3xl flex items-center justify-center text-3xl font-extrabold shadow-xl">
            JD
          </div>
          <div>
            <h2 className="text-3xl font-syne font-extrabold text-cyprus">Jean Dupont</h2>
            <p className="text-cyprus/60">Chauffeur Senior • Yaoundé, CM</p>
          </div>
        </div>
        <div className="flex gap-3">
          <Button variant="outline" className="gap-2"><PhoneCall size={18}/> Appeler</Button>
          <Button variant="primary">Modifier Profil</Button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card title="Détails Personnels" className="lg:col-span-2">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <DetailItem label="Numéro de Permis" value="ABC-12345-XYZ" icon={<Award size={16}/>} />
            <DetailItem label="Date d'embauche" value="12 Janvier 2024" icon={<Calendar size={16}/>} />
            <DetailItem label="Téléphone" value="+237 6XX XX XX XX" icon={<Phone size={16}/>} />
            <DetailItem label="Catégorie Permis" value="B, C, D" icon={<FileText size={16}/>} />
          </div>
        </Card>

        <Card title="Statistiques">
          <div className="space-y-4">
            <div className="text-center p-4 bg-sand rounded-2xl">
              <p className="text-3xl font-syne font-bold text-cyprus">1,240</p>
              <p className="text-xs text-cyprus/60 uppercase font-bold">KM Parcourus</p>
            </div>
            <div className="text-center p-4 bg-kiwi/10 rounded-2xl border border-kiwi/20">
              <p className="text-3xl font-syne font-bold text-kiwi-dark">4.8/5</p>
              <p className="text-xs text-cyprus/60 uppercase font-bold">Note Performance</p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
};

const DetailItem = ({ label, value, icon }) => (
  <div className="flex items-start gap-3">
    <div className="p-2 bg-sand-light rounded-lg text-cyprus/50">{icon}</div>
    <div>
      <p className="text-xs font-bold text-cyprus/40 uppercase tracking-wider">{label}</p>
      <p className="font-medium text-cyprus">{value}</p>
    </div>
  </div>
);

export default DriverDetail;