export const Card = ({ children, title, className = '', footer }) => {
  return (
    <div className={`bg-white rounded-2xl shadow-sm border border-sand-dark overflow-hidden ${className}`}>
      {title && (
        <div className="px-6 py-4 border-b border-sand-dark">
          <h3 className="font-syne font-bold text-cyprus text-lg">{title}</h3>
        </div>
      )}
      <div className="p-6">{children}</div>
      {footer && <div className="px-6 py-4 bg-sand-light border-t border-sand-dark">{footer}</div>}
    </div>
  );
};