import React from 'react';

export const PageHeader = ({ title, subtitle, action }) => {
  return (
    <div className="flex items-end justify-between mb-8">
      <div>
        <h2 className="text-3xl font-syne font-extrabold text-cyprus">{title}</h2>
        {subtitle && <p className="text-cyprus/60 font-sans mt-1">{subtitle}</p>}
      </div>
      {action && <div>{action}</div>}
    </div>
  );
};