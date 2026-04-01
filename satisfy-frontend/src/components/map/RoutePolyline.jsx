import React from 'react';
import { Polyline } from 'react-leaflet';

export const RoutePolyline = ({ positions }) => {
  // positions est un tableau de [lat, lng]
  if (!positions || positions.length < 2) return null;

  return (
    <Polyline 
      positions={positions} 
      pathOptions={{ 
        color: '#004643', // Cyprus
        weight: 4,
        opacity: 0.6,
        dashArray: '10, 10', // Style pointillé pour le trajet
        lineJoin: 'round'
      }} 
    />
  );
};