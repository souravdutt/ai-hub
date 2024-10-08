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
                    DEFAULT: '#4f43ef',
                    50: '#ededee',
                    100: '#dbdae7',
                    200: '#c2c0dd',
                    300: '#9a96d4',
                    400: '#6d66d6',
                    500: '#4f43ef',
                    600: '#261cba',
                    700: '#1b1297',
                    800: '#10097c',
                    900: '#070245',
                },
            },
        },
    },

    plugins: [forms],

    darkMode: 'selector',
};
