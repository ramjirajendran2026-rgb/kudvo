<?php

namespace App\Livewire\Pages;

use App\Models\Election;
use App\Models\Organisation;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * @property Form $form
 */
class VoteNow extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.pages.vote-now')
            ->layoutData([
                'seoData' => new SEOData(
                    title: __('pages/vote-now.seo.title'),
                    description: __('pages/vote-now.seo.title'),
                    schema: SchemaCollection::make()
                        ->add(fn (SEOData $data): array => [
                            '@context' => 'https://schema.org',
                            '@type' => 'BreadcrumbList',
                            'itemListElement' => [
                                [
                                    '@type' => 'ListItem',
                                    'position' => 1,
                                    'name' => 'Home',
                                    'item' => config('app.url'),
                                ],
                                [
                                    '@type' => 'ListItem',
                                    'position' => 2,
                                    'name' => 'Vote Now',
                                ],
                            ],
                        ]),
                ),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make(__('pages/vote-now.content.form.heading'))
                    ->description(__('pages/vote-now.content.form.description'))
                    ->schema([
                        ToggleButtons::make('has_election_code')
                            ->boolean()
                            ->default(true)
                            ->inline()
                            ->label(__('pages/vote-now.content.form.fields.has_election_code.label'))
                            ->live()
                            ->required(),

                        TextInput::make('election_code')
                            ->exists('elections', 'code')
                            ->extraInputAttributes([
                                'class' => 'font-mono tracking-[1em] uppercase font-semibold text-lg',
                            ])
                            ->helperText(__('pages/vote-now.content.form.fields.election_code.helper_text'))
                            ->label(__('pages/vote-now.content.form.fields.election_code.label'))
                            ->mask('EL********')
                            ->required()
                            ->visible(fn (Get $get): bool => (bool) $get('has_election_code')),

                        Select::make('organisation_id')
                            ->getOptionLabelUsing(fn ($value): ?string => Organisation::find($value)?->name)
                            ->getSearchResultsUsing(
                                fn (string $search): array => Organisation::search($search)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->hidden(fn (Get $get): bool => (bool) $get('has_election_code'))
                            ->label(__('pages/vote-now.content.form.fields.organisation_id.label'))
                            ->placeholder(__('pages/vote-now.content.form.fields.organisation_id.placeholder'))
                            ->live()
                            ->required()
                            ->searchable(),

                        Select::make('election_id')
                            ->hidden(fn (Get $get): bool => (bool) $get('has_election_code'))
                            ->label(__('pages/vote-now.content.form.fields.election_id.label'))
                            ->placeholder(__('pages/vote-now.content.form.fields.election_id.placeholder'))
                            ->options(
                                fn (Get $get) => Election::where('organisation_id', $get('organisation_id'))
                                    ->whereNotNull('published_at')
                                    ->select(['id', 'name', 'code'])
                                    ->latest()
                                    ->get()
                                    ->mapWithKeys(fn (Election $election) => [$election->id => $election->code . ' - ' . $election->name])
                            )
                            ->required()
                            ->searchable()
                            ->visible(fn (Get $get): bool => (bool) $get('organisation_id')),

                        Actions::make([
                            Action::make('submit')
                                ->extraAttributes(['wire:loading.class' => 'opacity-50'])
                                ->icon('heroicon-s-arrow-right')
                                ->label(__('pages/vote-now.content.form.actions.proceed.label'))
                                ->submit('proceed'),
                        ])->alignCenter(),
                    ]),
            ]);
    }

    public function proceed(): void
    {
        $data = $this->form->getState();

        $hasElectionCode = $data['has_election_code'];

        $election = $hasElectionCode
            ? Election::firstWhere('code', $data['election_code'])
            : Election::find($data['election_id']);

        if ($election->is_draft) {
            throw ValidationException::withMessages([
                $hasElectionCode ? 'data.election_code' : 'data.election_id' => 'The selected election code is invalid.',
            ]);
        }

        $this->redirect(route('filament.election.auth.login', $election));
    }
}
