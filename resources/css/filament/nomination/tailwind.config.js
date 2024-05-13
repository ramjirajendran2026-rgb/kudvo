import preset from '../../../../vendor/filament/filament/tailwind.config.preset';

export default {
    presets: [preset],
    content: [
        './app/Filament/Nomination/**/*.php',
        './resources/views/filament/nomination/**/*.blade.php',

        './app/Filament/Base/**/*.php',
        './resources/views/filament/base/**/*.blade.php',

        './app/Forms/**/*.php',
        './resources/views/forms/**/*.blade.php',

        './vendor/filament/**/*.blade.php',
    ],
};
