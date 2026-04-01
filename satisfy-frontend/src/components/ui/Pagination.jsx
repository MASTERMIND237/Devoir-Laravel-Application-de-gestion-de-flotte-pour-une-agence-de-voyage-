import { ChevronLeft, ChevronRight } from 'lucide-react';

export const Pagination = ({ currentPage, totalPages, onPageChange }) => {
  return (
    <div className="flex items-center justify-between px-4 py-3 bg-white border-t border-sand-dark rounded-b-xl">
      <div className="flex gap-2">
        <button 
          onClick={() => onPageChange(currentPage - 1)}
          disabled={currentPage === 1}
          className="p-2 rounded-lg hover:bg-sand disabled:opacity-30 text-cyprus"
        >
          <ChevronLeft size={20} />
        </button>
        <span className="flex items-center text-sm font-medium text-cyprus">
          Page {currentPage} sur {totalPages}
        </span>
        <button 
          onClick={() => onPageChange(currentPage + 1)}
          disabled={currentPage === totalPages}
          className="p-2 rounded-lg hover:bg-sand disabled:opacity-30 text-cyprus"
        >
          <ChevronRight size={20} />
        </button>
      </div>
    </div>
  );
};