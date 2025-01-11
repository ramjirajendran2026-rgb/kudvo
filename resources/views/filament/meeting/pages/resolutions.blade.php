<x-filament-panels::page
    :full-height="true"
>
    @php($notice = $this->getNotice())

    @if(filled($notice))
        <x-filament::section>
            <div class="prose max-w-none">
                {!! $notice !!}
            </div>
        </x-filament::section>
    @endif

    <form wire:submit="submit">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
