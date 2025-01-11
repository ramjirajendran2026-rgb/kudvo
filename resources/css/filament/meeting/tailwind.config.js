import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Meeting/**/*.php',
        './resources/views/filament/meeting/**/*.blade.php',

        './app/Filament/Base/**/*.php',
        './resources/views/filament/base/**/*.blade.php',

        './app/Forms/**/*.php',
        './resources/views/forms/**/*.blade.php',

        './app/Tables/**/*.php',
        './resources/views/tables/**/*.blade.php',

        './vendor/filament/**/*.blade.php',
    ],
}
