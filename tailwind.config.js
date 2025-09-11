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
                heavy: '#323B2F',
                bison: '#C3BBA4',
                envy: '#8DA894',
                granny: '#899A9C',
                gray: 'oklch(13% 0.028 261.692)',
            },
        },
    },

    plugins: [typography,forms],
};
