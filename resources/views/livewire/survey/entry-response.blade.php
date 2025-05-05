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

    {{ $this->responseInfolist }}

    <x-filament.nomination.footer class="text-center" />

    <x-filament-actions::modals />
</div>
