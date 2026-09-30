import defaultTheme from 'tailwindcss/defaultTheme';

/**
 * TokoOnline — single source of truth untuk design token.
 * Migrasi dari cdn.tailwindcss.com (inline config di 9 blade) ke build lokal Vite.
 * Primer: brand indigo · Aksen: accent fuchsia · Netral: stone · Font: Inter + Playfair Display
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', 'Georgia', 'serif'],
            },
            colors: {
                // Identitas utama (dulu inline di storefront.blade.php + customer/layout)
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                // Aksen marketing (hero, badge)
                accent: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef',
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                },
                // Aksen hangat (rating, badge Best)
                warm: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                },
            },
            keyframes: {
                floatSlow: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-12px)' },
                },
                fadeSlideUp: {
                    '0%': { transform: 'translateY(40px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(.85)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                slideInRight: {
                    '0%': { transform: 'translateX(100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },
            animation: {
                'float-slow': 'floatSlow 5s ease-in-out infinite',
                'float-slow-delayed': 'floatSlow 5s ease-in-out 1.5s infinite',
                'float-slow-delayed-2': 'floatSlow 5s ease-in-out 3s infinite',
                'fade-slide-up': 'fadeSlideUp .7s cubic-bezier(.16,1,.3,1) forwards',
                'scale-in': 'scaleIn .6s cubic-bezier(.16,1,.3,1) forwards',
                'slide-in-right': 'slideInRight .35s cubic-bezier(.16,1,.3,1) forwards',
            },
        },
    },
    plugins: [],
};
