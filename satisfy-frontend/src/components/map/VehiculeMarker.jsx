import React from 'react';
import { Marker, Popup } from 'react-leaflet';
import L from 'leaflet';
import { Car } from 'lucide-react';
import { renderToString } from 'react-dom/server';

// Création d'une icône personnalisée avec Lucide et nos couleurs
const createCustomIcon = (status) => {
  const color = status === 'en_route' ? '#89e900' : '#004643'; // Kiwi si en route, Cyprus sinon
  const html = renderToString(
    <div style={{ 
      backgroundColor: 'white', 
      padding: '8px', 
      borderRadius: '50%', 
      border: `3px solid ${color}`,
      boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1)'
    }}>
      <Car size={20} color={color} strokeWidth={3} />
    </div>
  );

  return L.divIcon({
    html,
    className: 'custom-vehicle-icon',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
  });
};

export const VehiculeMarker = ({ vehicule }) => {
  const { latitude, longitude, immatriculation, modele, statut } = vehicule;

  if (!latitude || !longitude) return null;

  return (
    <Marker position={[latitude, longitude]} icon={createCustomIcon(statut)}>
      <Popup className="custom-popup">
        <div className="font-sans p-1">
          <p className="font-syne font-bold text-cyprus">{immatriculation}</p>
          <p className="text-xs text-gray-500">{modele}</p>
          <div className={`mt-2 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full inline-block ${
            statut === 'en_route' ? 'bg-kiwi/20 text-cyprus' : 'bg-sand-dark text-cyprus'
          }`}>
            {statut.replace('_', ' ')}
          </div>
        </div>
      </Popup>
    </Marker>
  );
};