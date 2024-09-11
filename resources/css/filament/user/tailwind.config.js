import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './resources/views/components/**/*.blade.php',

        './app/Filament/User/**/*.php',
        './resources/views/filament/user/**/*.blade.php',

        './app/Filament/Base/**/*.php',
        './resources/views/filament/base/**/*.blade.php',

        './app/Forms/**/*.php',
        './resources/views/forms/**/*.blade.php',

        './app/Tables/**/*.php',
        './resources/views/tables/**/*.blade.php',

        './vendor/filament/**/*.blade.php',

        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',

        './vendor/bezhansalleh/filament-language-switch/resources/views/language-switch.blade.php',
    ],
}
