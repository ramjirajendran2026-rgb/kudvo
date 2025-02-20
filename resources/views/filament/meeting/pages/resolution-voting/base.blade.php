<x-filament-panels::page>
    @livewire('meeting.resolution-response-form', ['meeting' => $this->getMeeting(), 'isPreview' => $this->isPreview()])
</x-filament-panels::page>
