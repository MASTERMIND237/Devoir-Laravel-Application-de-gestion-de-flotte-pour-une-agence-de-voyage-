import React from 'react';
import { MapContainer, TileLayer, ZoomControl } from 'react-leaflet';
import { VehiculeMarker } from './VehiculeMarker';
import 'leaflet/dist/leaflet.css';

export const FlotteMap = ({ vehicules = [], center = [3.848, 11.502], zoom = 13 }) => {
  return (
    <div className="h-[500px] w-full rounded-3xl overflow-hidden border-4 border-white shadow-xl relative z-10">
      <MapContainer 
        center={center} 
        zoom={zoom} 
        style={{ height: '100%', width: '100%' }}
        zoomControl={false} // On le désactive pour le repositionner proprement
      >
        {/* Style de carte minimaliste "Voyager" de CartoDB (très propre avec notre charte Sand/Cyprus) */}
        <TileLayer
          url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        />
        
        <ZoomControl position="bottomright" />

        {vehicules.map((v) => (
          <VehiculeMarker key={v.id} vehicule={v} />
        ))}
      </MapContainer>
    </div>
  );
};