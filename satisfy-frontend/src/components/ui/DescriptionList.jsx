// src/components/ui/DescriptionList.jsx
export const DescriptionItem = ({ label, value, subValue }) => (
  <div className="flex justify-between items-center py-3 border-b border-sand-dark last:border-0">
    <span className="text-sm text-cyprus/60 font-medium">{label}</span>
    <div className="text-right">
      <p className="text-sm font-bold text-cyprus">{value}</p>
      {subValue && <p className="text-[10px] text-kiwi-dark font-bold uppercase">{subValue}</p>}
    </div>
  </div>
);