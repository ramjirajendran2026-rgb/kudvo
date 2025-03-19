@php
    use App\Enums\SurveyResponsesPageTabs;
@endphp

<x-filament-panels::page>
    <x-filament::tabs>
        @foreach ($this->getTabs() as $tab)
            <x-filament::tabs.item
                :active="$tab === $this->activeTab"
                wire:click="$set('activeTab', '{{ $tab->value }}')"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament::loading-indicator
        wire:loading
        wire:target="activeTab"
        class="mx-auto h-5 w-5 text-primary-500 dark:text-primary-400"
    />

    @if ($this->activeTab === SurveyResponsesPageTabs::Summary)
        <x-filament::section compact>
            <h1
                class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl"
            >
                {{ $this->getRecordTitle() }}
            </h1>
        </x-filament::section>

        @foreach ($this->getSummaryItems() as $question)
            <x-filament::section
                :compact="true"
                :heading="$question->text"
                :description="$question->answers->sum() . ' responses'"
            >
                <ul class="max-h-80 space-y-1 overflow-y-auto">
                    @forelse ($question->answers as $answer => $count)
                        <li
                            class="flex items-center gap-4 rounded-lg bg-primary-50 px-2 py-1 dark:bg-primary-900/20"
                        >
                            {!! $question->type->getAnswerOutput($question, $answer) !!}
                            <x-filament::badge color="success" size="sm">
                                {{ $count }}
                            </x-filament::badge>
                        </li>
                    @empty
                        <li>
                            <x-filament.state
                                heading=""
                                icon="heroicon-o-x-mark"
                                class="!px-0 !py-0 [&_.mb-4]:!mb-0 [&_.mb-4]:!p-1"
                                description="No responses have been submitted yet."
                            />
                        </li>
                    @endforelse
                </ul>
            </x-filament::section>
        @endforeach
    @elseif ($this->activeTab === SurveyResponsesPageTabs::Individual)
        <div class="flex-row items-center justify-center">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="activeResponseId">
                    @foreach ($this->getResponseNumbers() as $id => $number)
                        <option value="{{ $id }}">
                            Response {{ $number }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
            <span class="text-sm text-gray-950 dark:text-white">
                Submitted on
                {{ $this->getActiveResponse()?->created_at->timezone(filament()->getTenant()?->timezone)->format('d M, Y h:i:s A') }}
            </span>
        </div>

        <x-filament::loading-indicator
            wire:loading
            wire:target="activeResponseId"
            class="mx-auto h-5 w-5 text-primary-500 dark:text-primary-400"
        />

        <div
            class="[&>.max-w-screen-md]:!p-0"
            wire:target="activeResponseId"
            wire:loading.class="hidden"
        >
            @livewire('survey.entry-form', ['survey' => $this->getRecord(), 'isDisabled' => true, 'data' => $this->activeResponseData()], key($this->activeResponseId))
        </div>
    @endif
</x-filament-panels::page>
