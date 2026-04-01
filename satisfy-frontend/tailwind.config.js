/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        sand: {
          DEFAULT: '#f0ede5',
          light: '#f7f5f0',
          dark: '#e8e4d9',
        },
        cyprus: {
          DEFAULT: '#004643',
          mid: '#1a6b67',
          dark: '#002e2c',
        },
        kiwi: {
          DEFAULT: '#89e900',
          dark: '#6bb800',
        },
      },
      fontFamily: {
        // Principal: Poppins pour le corps et les UI
        poppins: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        // Titres: Josefin Sans pour un look élégant et condensé
        josefin: ['Josefin Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        // Garde-fous / héritage
        syne: ['Syne', 'sans-serif'],
        sans: ['Poppins', 'DM Sans', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-out',
        'slide-up': 'slideUp 0.4s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
      },
    },
  },
  plugins: [],
}
