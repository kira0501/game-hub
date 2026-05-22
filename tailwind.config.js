import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/View/Components/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hub: {
                    bg: '#0b1118',
                    panel: '#111a24',
                    panel2: '#172331',
                    cyan: '#22d3ee',
                    blue: '#38bdf8',
                    green: '#67e8f9',
                },
            },
        },
    },
    plugins: [forms],
};
