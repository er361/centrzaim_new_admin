/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.{js,jsx}",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                'numbers': ['Lato', 'sans-serif'],
                'body': ['Monsterrat', 'sans-serif'],
                'golos': ['Golos', 'sans-serif'],
            },
            colors: {
                'black-text': '#1E2033',
                'red': '#F4555A',
                'gray-bg': '#F3F3F7',
                'blue': '#2F76E2',
                'accent-gray': '#EDF2FE',
                'gray-1' : '#484E63'
            },
            container: {
                center: true,
                padding:{
                    DEFAULT: '1rem',
                    sm: '2rem',
                    lg: '4rem',
                    xl: 0,
                    '2xl': 0,
                }
            },
            screens: {
                'sm': '640px',
                // => @media (min-width: 640px) { ... }

                'md': '768px',
                // => @media (min-width: 768px) { ... }

                'lg': '1024px',
                // => @media (min-width: 1024px) { ... }

                'xl': '1280px',
                // => @media (min-width: 1280px) { ... }

                '2xl': '1300px',
                // => @media (min-width: 1536px) { ... }
                '3xl': '1400px',
            }
        },
    },
    plugins: [],
}

