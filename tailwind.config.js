import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#aa3cdd',
                    50: '#edecee',
                    100: '#ddd6e0',
                    200: '#ccb8d5',
                    300: '#bf92d3',
                    400: '#b365d7',
                    500: '#aa3cdd',
                    600: '#9516d0',
                    700: '#6e0d9b',
                    800: '#3a0255',
                    900: '#220132',
                },
            },
        },
    },

    plugins: [forms],

    darkMode: 'selector',
};
