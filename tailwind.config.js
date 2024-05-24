import preset from './vendor/filament/support/tailwind.config.preset.js';

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',

        './resources/views/components/**/*.blade.php',

        './app/Livewire/**/*.php',
        './resources/views/livewire/**/*.blade.php',

        './vendor/filament/**/*.blade.php',

        './vendor/bezhansalleh/filament-language-switch/resources/views/language-switch.blade.php',
    ],
    theme: {
        extend: {
            keyframes: {
                'infinite-scroll': {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-100%)' },
                },
            },
            animation: {
                'infinite-scroll': 'infinite-scroll 25s linear infinite',
            },
        },
    },
    plugins: [
        function ({ addUtilities }) {
            addUtilities({
                '.bg-var-url': {
                    'background-image': 'var(--bg-url)',
                },
            });
        },
    ],
};
