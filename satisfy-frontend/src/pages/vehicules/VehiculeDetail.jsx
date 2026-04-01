import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { ChevronLeft, MapPin, Gauge, Fuel, Calendar } from 'lucide-react';
import { Card } from '../../components/ui/Card';
import { Badge } from '../../components/ui/Badge';
import { Button } from '../../components/ui/Button';

const VehiculeDetail = () => {
  const { id } = useParams();
  // Ici on appellerait un hook type useVehicule(id)
  
  return (
    <div className="space-y-6">
      <Link to="/vehicules" className="flex items-center gap-2 text-cyprus/60 hover:text-cyprus transition-colors mb-4">
        <ChevronLeft size={20} /> Retour à la liste
      </Link>

      <div className="flex justify-between items-start">
        <div>
          <h2 className="text-4xl font-syne font-extrabold text-cyprus tracking-tight">LT-882-CI</h2>
          <p className="text-xl text-cyprus/60 font-sans">Toyota Hilux 2024</p>
        </div>
        <Badge variant="success">En Route</Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <Card title="Informations Techniques" className="lg:col-span-2">
          <div className="grid grid-cols-2 md:grid-cols-3 gap-8">
            <div className="space-y-1">
              <p className="text-xs text-cyprus/40 uppercase font-bold tracking-wider">Type</p>
              <p className="font-medium">Pickup 4x4</p>
            </div>
            <div className="space-y-1">
              <p className="text-xs text-cyprus/40 uppercase font-bold tracking-wider">Énergie</p>
              <p className="font-medium">Diesel</p>
            </div>
            <div className="space-y-1">
              <p className="text-xs text-cyprus/40 uppercase font-bold tracking-wider">Transmission</p>
              <p className="font-medium">Manuelle</p>
            </div>
          </div>
        </Card>

        <Card title="Stats Rapides">
          <div className="space-y-4">
            <div className="flex items-center justify-between border-b border-sand-dark pb-2">
              <span className="flex items-center gap-2 text-sm text-cyprus/60"><Gauge size={16}/> Kilométrage</span>
              <span className="font-bold">45,200 km</span>
            </div>
            <div className="flex items-center justify-between border-b border-sand-dark pb-2">
              <span className="flex items-center gap-2 text-sm text-cyprus/60"><Fuel size={16}/> Conso. Moy.</span>
              <span className="font-bold">8.5 L</span>
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
};

export default VehiculeDetail;