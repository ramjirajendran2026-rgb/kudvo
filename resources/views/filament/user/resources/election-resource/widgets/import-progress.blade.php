<x-filament-widgets::widget>
    <x-filament::section
        :compact="true"
        :heading="$this->getHeading()"
        :description="$this->getDescription()"
    >
        <div class="flex justify-between mb-1">
            <span class="text-base font-medium text-primary-700 dark:text-white">{{ $this->getImport()->file_name }}</span>
            <span class="text-sm font-medium text-primary-700 dark:text-white">{{ $this->getProgress() }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $this->getPercentage() }}"></div>
        </div>

        @if($this->downloadFailedRowsAction->isVisible())
            <div class="mt-2">
                {{ $this->downloadFailedRowsAction }}
            </div>
        @endif

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
