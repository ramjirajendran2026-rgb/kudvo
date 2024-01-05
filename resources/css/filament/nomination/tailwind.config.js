import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Nomination/**/*.php',
        './resources/views/filament/nomination/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
