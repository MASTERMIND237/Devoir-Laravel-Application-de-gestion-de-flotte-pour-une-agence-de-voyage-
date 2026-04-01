export const Table = ({ headers, children }) => {
  return (
    <div className="w-full overflow-x-auto rounded-xl border border-sand-dark">
      <table className="w-full text-left border-collapse bg-white">
        <thead className="bg-cyprus text-white font-syne">
          <tr>
            {headers.map((header, i) => (
              <th key={i} className="px-6 py-4 text-sm font-semibold uppercase tracking-wider">{header}</th>
            ))}
          </tr>
        </thead>
        <tbody className="divide-y divide-sand-dark">
          {children}
        </tbody>
      </table>
    </div>
  );
};