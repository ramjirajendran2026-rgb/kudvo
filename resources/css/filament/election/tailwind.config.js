import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Election/**/*.php',
        './resources/views/filament/election/**/*.blade.php',

        './app/Forms/**/*.php',
        './resources/views/forms/**/*.blade.php',

        './vendor/filament/**/*.blade.php',
    ],
}
