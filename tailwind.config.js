import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
            },
        },
    },

    plugins: [forms],

    // Classes used only inside Alpine x-bind:class (JIT may miss them)
    safelist: [
        'ring-2',
        'ring-brand-500',
        'ring-brand-500/60',
        'ring-offset-2',
        'ring-offset-slate-50',
        'ring-offset-slate-950',
        'ring-offset-white',
        'md:scale-[1.01]',
        'lg:scale-[1.02]',
        'opacity-95',
        'shadow-brand-500/25',
        'shadow-brand-500/40',
        'shadow-brand-500/30',
        'shadow-brand-500/5',
        'ring-brand-400/70',
        'from-brand-950/40',
        'to-brand-950/40',
        'ring-offset-[#0c1220]',
    ],
};
