import React from 'react';
import { 
  AreaChart, Area, XAxis, YAxis, 
  CartesianGrid, Tooltip, ResponsiveContainer 
} from 'recharts';

export const KmChart = ({ data }) => {
  return (
    <div className="h-[300px] w-full">
      <ResponsiveContainer width="100%" height="100%">
        <AreaChart data={data} margin={{ top: 10, right: 10, left: 0, bottom: 0 }}>
          <defs>
            <linearGradient id="colorKm" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#89e900" stopOpacity={0.3}/>
              <stop offset="95%" stopColor="#89e900" stopOpacity={0}/>
            </linearGradient>
          </defs>
          <XAxis 
            dataKey="name" 
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
            contentStyle={{ 
              borderRadius: '12px', 
              border: 'none', 
              boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)',
              backgroundColor: '#f7f5f0' 
            }}
          />
          <Area 
            type="monotone" 
            dataKey="km" 
            stroke="#89e900" 
            strokeWidth={3}
            fillOpacity={1} 
            fill="url(#colorKm)" 
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
};