import React from 'react';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  Tooltip, 
  ResponsiveContainer 
} from 'recharts';

export const MaintenanceChart = ({ data }) => {
  // On s'assure que chaque donnée a une propriété 'fill' si on veut des couleurs alternées
  // Cela évite d'utiliser le composant Cell
  const chartData = data.map((entry, index) => ({
    ...entry,
    fill: index % 2 === 0 ? '#004643' : '#89e900', // Cyprus ou Kiwi
  }));

  return (
    <div className="h-[300px] w-full">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart 
          data={chartData} 
          margin={{ top: 10, right: 10, left: 0, bottom: 0 }}
        >
          <XAxis 
            dataKey="label" 
            axisLine={false} 
            tickLine={false} 
            tick={{ fill: '#004643', fontSize: 12, fontWeight: 500 }}
            dy={10}
          />
          <YAxis 
            axisLine={false} 
            tickLine={false} 
            tick={{ fill: '#004643', fontSize: 12 }} 
          />
          <Tooltip 
            cursor={{ fill: '#e8e4d9', opacity: 0.4 }}
            contentStyle={{ 
              borderRadius: '12px', 
              border: 'none', 
              boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)',
              fontFamily: 'DM Sans'
            }}
          />
          {/* On passe la propriété "fill" directement au Bar. 
            Recharts ira chercher la clé "fill" dans chaque objet de data 
          */}
          <Bar 
            dataKey="value" 
            radius={[6, 6, 0, 0]} 
            barSize={40}
          />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
};