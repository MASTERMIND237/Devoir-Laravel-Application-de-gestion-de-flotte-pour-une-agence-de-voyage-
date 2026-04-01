import React from 'react';

export const Button = ({ children, variant = 'primary', className = '', isLoading = false, ...props }) => {
  const baseStyles = "px-6 py-2.5 rounded-xl font-syne font-bold transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95";
  
  const variants = {
    primary: "bg-kiwi text-cyprus-dark hover:bg-kiwi-dark shadow-sm",
    secondary: "bg-cyprus text-white hover:bg-cyprus-mid",
    outline: "border-2 border-cyprus text-cyprus hover:bg-cyprus hover:text-white",
    ghost: "text-cyprus hover:bg-sand-dark"
  };

  return (
    <button 
      className={`${baseStyles} ${variants[variant]} ${className}`}
      disabled={isLoading || props.disabled}
      {...props}
    >
      {isLoading ? (
        <div className="w-5 h-5 border-2 border-current border-t-transparent rounded-full animate-spin" />
      ) : children}
    </button>
  );
};