import preset from './vendor/filament/support/tailwind.config.preset.js'

const defaultTheme = require('tailwindcss/defaultTheme')

export default {
    presets: [preset],
    content: [
        './app/Forms/**/*.php',
        './resources/views/forms/**/*.blade.php',

        './app/Tables/**/*.php',
        './resources/views/tables/**/*.blade.php',

        './resources/views/components/**/*.blade.php',

        './resources/views/pdf/**/*.blade.php',

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
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        function ({ addUtilities }) {
            addUtilities({
                '.bg-var-url': {
                    'background-image': 'var(--bg-url)',
                },
                '.glass': {
                    background: 'rgba(255, 255, 255, 0.65)',
                    'box-shadow': '0 8px 32px 0 rgba(31, 38, 135, 0.12)',
                    'backdrop-filter': 'blur(7px)',
                    '-webkit-backdrop-filter': 'blur(7px)',
                    'border-radius': '18px',
                    border: '1px solid rgba(255,255,255,0.18)',
                },
                '.neumorph': {
                    'box-shadow': '8px 8px 24px #e3e8f0, -8px -8px 24px #fff',
                },
                '.btn-primary': {
                    background:
                        'linear-gradient(90deg, #2563eb 0%, #1e40af 100%)',
                    color: '#fff',
                    'border-radius': '9999px',
                    'box-shadow': '0 2px 8px 0 #2563eb33',
                    'font-weight': '600',
                    transition:
                        'background 0.2s, box-shadow 0.2s, transform 0.1s',
                },
                '.btn-primary:hover, .btn-primary:focus': {
                    background: '#1e40af',
                    transform: 'scale(1.04)',
                    'box-shadow': '0 4px 16px 0 #2563eb44',
                },
                '.focus-outline:focus': {
                    outline: '2px solid #2563eb',
                    'outline-offset': '2px',
                },
            })
        },
    ],
}
