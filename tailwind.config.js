import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Newsreader', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                atlas: {
                    background: 'var(--color-atlas-background)',
                    surface: 'var(--color-atlas-surface)',
                    panel: 'var(--color-atlas-panel)',
                    border: 'var(--color-atlas-border)',
                    text: 'var(--color-atlas-text)',
                    muted: 'var(--color-atlas-muted)',
                    primaryStart: 'var(--color-atlas-primaryStart)',
                    primaryEnd: 'var(--color-atlas-primaryEnd)',
                }
            },
            boxShadow: {
                'ambient': '0 -5px 40px rgba(42, 52, 57, 0.06)',
            },
            borderRadius: {
                '3xl': '1.5rem', // 24px
            }
        },
    },

    plugins: [forms],
};
