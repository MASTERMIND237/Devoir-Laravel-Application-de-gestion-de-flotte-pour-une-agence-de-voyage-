export const Badge = ({ children, variant = 'neutral' }) => {
  const styles = {
    neutral: "bg-sand-dark text-cyprus-mid",
    success: "bg-kiwi/20 text-cyprus font-bold border border-kiwi",
    warning: "bg-orange-100 text-orange-700 border border-orange-200",
    danger: "bg-red-100 text-red-700 border border-red-200",
    info: "bg-cyprus/10 text-cyprus border border-cyprus/20"
  };

  return (
    <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${styles[variant]}`}>
      {children}
    </span>
  );
};