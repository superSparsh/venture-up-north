import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography'

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
                sans: ['Lato', ...defaultTheme.fontFamily.sans],
            },
            scrollBehavior: ['responsive'],
            colors: {
                heavy: '#86B9DF',
                bison: '#6A3F32',
                envy: '#CC6648',
                granny: '#3082BD',
                gray: 'oklch(13% 0.028 261.692)',
            },
        },
    },

    plugins: [typography,forms],
};
