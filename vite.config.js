import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                'resources/css/pdf.css',

                'resources/js/swal.js',

                'resources/css/filament/admin/theme.css',

                'resources/css/filament/election/theme.css',
                'resources/js/filament/election/scripts.js',

                'resources/css/filament/meeting/theme.css',

                'resources/css/filament/nomination/theme.css',

                'resources/css/filament/user/theme.css',
            ],
            refresh: [...refreshPaths, 'app/Livewire/**'],
        }),
    ],
})
