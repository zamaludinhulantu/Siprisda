import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        // General utilities used conditionally
        'bg-gradient-to-b',
        'border-l-4',
        'shadow-xl',
        'shadow-2xl',
        'w-72',
        'sticky',
        'top-0',
        // Global blue (sky + cyan)
        'from-sky-900',
        'to-sky-800',
        'bg-sky-800',
        'bg-sky-50',
        'border-sky-700',
        'hover:bg-sky-800',
        'text-sky-700',
        'text-sky-800',
        'text-sky-900',
        'border-sky-300',
        'bg-sky-50',
        'bg-sky-100',
        'text-cyan-200',
        'text-cyan-300',
        'border-cyan-400',
        'border-cyan-500',
        'focus:border-cyan-600',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
