<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Enums\Alignment;

class Preference extends NominationPage
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.user.resources.nomination-resource.pages.preference';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $activeNavigationIcon = 'heroicon-s-cog-6-tooth';

    public static string | Alignment $formActionsAlignment = Alignment::End;

    public ?array $data = [];

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->form->fill($this->getNomination()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: ! $this->canSave())
            ->schema(components: [
                Group::make()
                    ->relationship(name: 'preference')
                    ->schema(components: [
                        Section::make(heading: 'MFA Code Delivery')
                            ->description(description: 'Multi-Factor Authentication code for each voters will be sent through this medium')
                            ->columns(columns: 3)
                            ->columnSpan(span: 1)
                            ->schema([
                                Toggle::make(name: 'mfa_mail')
                                    ->label(label: 'Email')
                                    ->default(state: true),

                                Toggle::make(name: 'mfa_sms')
                                    ->label(label: 'SMS'),
                            ]),
                    ]),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                form: $this->makeForm()
                    ->operation(operation: 'edit')
                    ->model(model: $this->getNomination())
                    ->statePath(path: 'data')
            ),
        ];
    }

    public function getFormActions(): array
    {
        return [
            $this->getSaveAction(),
        ];
    }

    protected function getSaveAction(): Action
    {
        return Action::make(name: 'save')
            ->keyBindings(bindings: ['mod+s'])
            ->label(label: 'Save Preference')
            ->submit(form: 'save')
            ->visible(condition: $this->canSave());
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return parent::canAccessPage($nomination) &&
            static::can(action: 'viewPreference', nomination: $nomination);
    }

    protected function canSave(): bool
    {
        return static::can(action: 'savePreference', nomination: $this->getNomination());
    }

    public function save(): void
    {
        abort_unless(boolean: $this->canSave(), code: 403);

        $this->form->getState();

        $this->redirect(url: Dashboard::getUrl(parameters: [$this->getNomination()]));
    }
}
