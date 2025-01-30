@php
    use App\Models\Survey;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;
@endphp

<div class="p-2 lg:p-6">
    <x-filament::section class="mx-auto max-w-4xl">
        <h1 class="px-2 py-4 text-center text-2xl font-bold">
            {{ $survey->name }}
        </h1>

        <div class="prose max-w-none">
            {!! $survey->settings['description'] ?? null !!}
        </div>

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    </x-filament::section>

    @if ($survey->hasMedia(Survey::MEDIA_COLLECTION_FOOTER_IMAGES))
        <x-filament::section compact class="mx-auto mt-8 max-w-4xl">
            <div class="flex flex-wrap items-center justify-center gap-2">
                @php
                    /** @var Media $media */
                @endphp

                @foreach ($survey->getMedia(Survey::MEDIA_COLLECTION_FOOTER_IMAGES) as $media)
                    {{ $media->img(extraAttributes: ['class' => 'h-16 w-auto']) }}
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <x-filament-actions::modals />
</div>
