import { X } from 'lucide-react';

export const Modal = ({ isOpen, onClose, title, children }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-cyprus/40 backdrop-blur-sm">
      <div className="bg-sand w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
        <div className="flex items-center justify-between p-6 border-b border-sand-dark">
          <h2 className="text-xl font-syne font-bold text-cyprus">{title}</h2>
          <button onClick={onClose} className="p-2 hover:bg-sand-dark rounded-full transition-colors text-cyprus">
            <X size={20} />
          </button>
        </div>
        <div className="p-6">{children}</div>
      </div>
    </div>
  );
};