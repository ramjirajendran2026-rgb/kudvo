<?php

namespace App\Livewire\Pages\Products\Election;

use App\Forms\ElectorForm;
use App\Models\Election;
use App\Models\Elector;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\View\View;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * @property Form $form
 */
class BallotDemo extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.pages.products.election.ballot-demo')
            ->layoutData([
                'seoData' => new SEOData(
                    title: 'Try Voting Demo',
                    description: 'Experience online voting by filling out the form below.',
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
                                    'name' => 'Online Voting',
                                    'item' => route('products.election.home'),
                                ],
                                [
                                    '@type' => 'ListItem',
                                    'position' => 3,
                                    'name' => 'Demo Ballot',
                                ],
                            ],
                        ]),
                ),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(Elector::class)
            ->statePath('data')
            ->schema([
                Section::make('Ballot Demo')
                    ->description('Experience online voting by filling out the form below.')
                    ->schema([
                        Cluster::make(schema: [
                            ElectorForm::titleComponent()
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.title.placeholder')),

                            ElectorForm::firstNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.first_name.placeholder')),

                            ElectorForm::lastNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: __('filament.user.elector-resource.form.last_name.placeholder')),
                        ])
                            ->columns(columns: 5)
                            ->label(label: __('filament.user.elector-resource.form.full_name.label')),

                        Toggle::make('link_mail')
                            ->dehydrated(false)
                            ->label('Send voting link through email.')
                            ->live(),

                        ElectorForm::emailComponent()
                            ->visible(fn (Get $get): bool => (bool) $get('link_mail')),

                        Toggle::make('link_sms')
                            ->dehydrated(false)
                            ->label('Send voting link through SMS.')
                            ->live(),

                        ElectorForm::phoneComponent()
                            ->visible(fn (Get $get): bool => (bool) $get('link_sms')),

                        Actions::make([
                            Action::make('submit')
                                ->extraAttributes(['wire:loading.class' => 'opacity-50'])
                                ->label('Submit')
                                ->submit('proceed'),
                        ])->alignCenter(),
                    ]),
            ]);
    }

    public function proceed(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->js(
                <<<'JS'
Swal.fire({
    title: 'Too many requests',
    text: 'Please try again later',
    icon: 'error'
})
JS
            );

            return;
        }

        $data = $this->form->getState();

        $demoElectionCode = config('app.election.demo_election_code');
        if (blank($demoElectionCode) || blank($election = Election::firstWhere('code', $demoElectionCode))) {
            $this->js(
                <<<'JS'
Swal.fire({
    title: 'No demo found',
    text: 'No demo election available right now. Please check back later.',
    icon: 'warning'
})
JS
            );

            return;
        }

        $this->form->fill();

        do {
            $data['membership_number'] = fake()->bothify('??######');

            try {
                /** @var Elector $elector */
                $elector = $election->electors()->create($data);
            } catch (UniqueConstraintViolationException $exception) {
            }
        } while (! isset($elector));

        if ($election->preference->ballot_link_unique && (filled($elector->email) || filled($elector->phone))) {
            $elector->fresh()->sendBallotLink($election, now: true);

            $this->js(
                <<<'JS'
Swal.fire({
    title: 'Success',
    text: 'An unique voting link for demo ballot has been sent to your email address / phone number. Please use the given link to experience online voting.',
    icon: 'success'
})
JS
            );

            return;
        }

        $this->redirect(route('short_link.ballot', $elector));
    }
}
