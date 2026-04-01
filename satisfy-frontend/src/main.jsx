// import React from 'react'
// import ReactDOM from 'react-dom/client'

// ReactDOM.createRoot(document.getElementById('root')).render(
//   <h1 style={{ color: '#004643', padding: '50px' }}>Satisfy est en ligne !</h1>
// )




// import React from 'react';
// import ReactDOM from 'react-dom/client';
// import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
// import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
// import App from './App.jsx';
// import './index.css';

// // Configuration de React Query
// const queryClient = new QueryClient({
//   defaultOptions: {
//     queries: {
//       refetchOnWindowFocus: false, // Évite les refresh inutiles
//       retry: 1, // Réessaye une fois en cas d'échec
//       staleTime: 1000 * 60 * 5, // Les données sont considérées fraîches pendant 5 min
//     },
//   },
// });

// ReactDOM.createRoot(document.getElementById('root')).render(
//   <React.StrictMode>
//     <QueryClientProvider client={queryClient}>
//       <App />
//       {/* Devtools uniquement en dev pour déboguer ton API Laravel */}
//       <ReactQueryDevtools initialIsOpen={false} />
//     </QueryClientProvider>
//   </React.StrictMode>
// );












import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { ReactQueryDevtools } from '@tanstack/react-query-devtools'
import './index.css'
import App from './App.jsx'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      retry: 1,
      staleTime: 1000 * 60 * 5,
    },
  },
})

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <App />
      <ReactQueryDevtools initialIsOpen={false} />
    </QueryClientProvider>
  </StrictMode>,
)
