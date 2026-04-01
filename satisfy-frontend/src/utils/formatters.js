import dayjs from 'dayjs';
import 'dayjs/locale/fr';

dayjs.locale('fr');

export const formatters = {
  // Formatage de la monnaie (FCFA pour le Cameroun)
  currency: (amount) => {
    return new Intl.NumberFormat('fr-CM', {
      style: 'currency',
      currency: 'XAF',
      minimumFractionDigits: 0
    }).format(amount);
  },

  // Formatage des dates (ex: 30 Mars 2026)
  date: (date) => dayjs(date).format('DD MMMM YYYY'),
  
  // Formatage date et heure pour les maintenances
  dateTime: (date) => dayjs(date).format('DD/MM/YYYY [à] HH:mm'),

  // Distance avec unité
  distance: (km) => `${km.toLocaleString()} km`,

  // Capitalisation (ex: en_route -> En route)
  status: (text) => {
    if (!text) return '';
    const formatted = text.replace(/_/g, ' ');
    return formatted.charAt(0).toUpperCase() + formatted.slice(1);
  }
};