<div
    class="mx-auto flex w-full max-w-screen-md flex-col justify-center gap-y-6 px-4 py-6 sm:px-6 lg:px-8"
>
    <h1
        translate="no"
        class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl"
    >
        {{ $survey->title }}
    </h1>

    <div id="google_translate_element" class="print:hidden"></div>

    {{ $this->form }}

    <x-filament.nomination.footer class="text-center" />

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement(
                { includedLanguages: 'en,ta' },
                'google_translate_element',
            )
        }
    </script>

    <script
        type="text/javascript"
        src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"
    ></script>

    <x-filament-actions::modals />
</div>
