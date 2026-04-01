import React from 'react';

export const Input = ({ label, error, className = '', ...props }) => {
  return (
    <div className={`flex flex-col gap-1.5 w-full ${className}`}>
      {label && <label className="text-cyprus font-medium text-sm ml-1">{label}</label>}
      <input
        className={`bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 text-cyprus-dark focus:border-cyprus outline-none transition-colors placeholder:text-gray-400 ${error ? 'border-red-500' : ''}`}
        {...props}
      />
      {error && <span className="text-red-500 text-xs mt-1 ml-1">{error}</span>}
    </div>
  );
};