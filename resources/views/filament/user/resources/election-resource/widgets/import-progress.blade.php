<x-filament-widgets::widget>
    <x-filament::section
        :compact="true"
        :heading="$this->getHeading()"
        :description="$this->getDescription()"
    >
        <div class="mb-1 flex justify-between">
            <span
                class="text-base font-medium text-primary-700 dark:text-white"
            >
                {{ $this->getImport()->file_name }}
            </span>
            <span class="text-sm font-medium text-primary-700 dark:text-white">
                {{ $this->getProgress() }}
            </span>
        </div>
        <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
            <div
                class="h-2.5 rounded-full bg-primary-600"
                style="width: {{ $this->getPercentage() }}"
            ></div>
        </div>

        @if ($this->downloadFailedRowsAction->isVisible())
            <div class="mt-2">
                {{ $this->downloadFailedRowsAction }}
            </div>
        @endif

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
