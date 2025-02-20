<x-filament-panels::page>
    @livewire('meeting.resolution-response-form', ['meeting' => $this->getMeeting(), 'participant' => filament()->auth()->user(), 'isPreview' => $this->isPreview()])
</x-filament-panels::page>
