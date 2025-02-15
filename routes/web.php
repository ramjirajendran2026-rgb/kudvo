<?php

use App\Actions\Meeting\GenerateDetailedResultPdf;
use App\Filament\Election\Pages\Index as ElectionPanel;
use App\Http\Controllers\AwsSnsController;
use App\Http\Controllers\CheckoutController;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\Products\Election\BallotDemo;
use App\Livewire\Pages\Products\Election\Home as ElectionHome;
use App\Livewire\Pages\Products\Election\HowItWorks;
use App\Livewire\Pages\Products\Phygital\Home as PhygitalHome;
use App\Livewire\Pages\Products\Survey\Home as SurveyHome;
use App\Livewire\Pages\QsyssMeetingRegistration;
use App\Livewire\Pages\QsyssMeetingRegistrationResponses;
use App\Livewire\Pages\VoteNow;
use App\Livewire\Pages\Wiki\Index as WikiIndex;
use App\Livewire\Pages\Wiki\Show as WikiDetails;
use App\Livewire\Survey\EntryForm;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\ShortLink;
use App\Services\Clicksend\Http\Controllers\WebhookController as ClicksendWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(
    [
        'domain' => config('app.main_domain'),
    ],
    function () {
        Route::prefix(LaravelLocalization::setLocale())
            ->middleware([
                LocaleSessionRedirect::class,
                LaravelLocalizationRedirectFilter::class,
                LaravelLocalizationViewPath::class,
            ])
            ->group(function () {
                Livewire::setUpdateRoute(function ($handle) {
                    return Route::post('livewire/update', $handle)
                        ->name('i18n.livewire.update');
                });

                Route::get(uri: '/', action: Home::class)
                    ->name(name: 'home');

                Route::get(uri: 'vote-now', action: VoteNow::class)
                    ->name(name: 'vote-now');

                Route::prefix('products')
                    ->name('products.')
                    ->group(function (): void {
                        Route::prefix('online-voting')
                            ->name('election.')
                            ->group(function (): void {
                                Route::get(uri: '/', action: ElectionHome::class)
                                    ->name(name: 'home');

                                Route::get(uri: 'how-it-works', action: HowItWorks::class)
                                    ->name(name: 'how-it-works');

                                Route::get(uri: 'ballot-demo', action: BallotDemo::class)
                                    ->name(name: 'ballot-demo');
                            });

                        Route::get(uri: 'phygital-voting', action: PhygitalHome::class)
                            ->name(name: 'phygital.home');

                        Route::get(uri: 'survey', action: SurveyHome::class)
                            ->name(name: 'survey.home');
                    });
            });

        Route::prefix('wiki')
            ->name('wiki.')
            ->group(function () {
                Route::get(uri: '/', action: WikiIndex::class)
                    ->name(name: 'index');

                Route::get(uri: '/categories/{category}', action: WikiIndex::class)
                    ->name(name: 'categories.show');

                Route::get(uri: '/tags/{tag}', action: WikiIndex::class)
                    ->name(name: 'tags.show');

                Route::get('{page}', action: WikiDetails::class)
                    ->name('show');
            });

        Route::get(uri: 'privacy-policy', action: PrivacyPolicy::class)
            ->name(name: 'privacy-policy');

        Route::get(
            uri: 'e/{election:short_code}',
            action: fn (Election $election) => redirect(to: ElectionPanel::getUrl(parameters: ['election' => $election], panel: 'election'))
        )->name(name: 'short_link.election');

        Route::get(
            uri: 'b/{elector:short_code}',
            action: fn (Request $request, Elector $elector) => redirect(to: URL::signedRoute(name: 'filament.election.eul', parameters: ['election' => $elector->event, 'elector' => $elector]))
        )->name(name: 'short_link.ballot');

        Route::get(
            uri: 'g',
            action: function (Request $request) {
                if ($request->filled('b')) {
                    return redirect()->route('short_link.ballot', ['elector' => $request->get('b')]);
                }

                if ($request->filled('e')) {
                    return redirect()->route('short_link.election', ['election' => $request->get('e')]);
                }

                if ($request->filled('p')) {
                    /** @var Participant $participant */
                    $participant = Participant::with('meeting')->where('short_key', $request->get('p'))->firstOrFail();

                    return redirect()->signedRoute('filament.meeting.eul', ['participant' => $participant, 'meeting' => $participant->meeting]);
                }

                $shortLink = ShortLink::where('key', collect($request->query())->keys()->first())->firstOrFail();

                return redirect()->away($shortLink->destination);
            },
        )->name(name: 'short_link.go');

        Route::get('meeting', QsyssMeetingRegistration::class)
            ->name('qsyss-meeting.registration');

        Route::get('meeting/responses', QsyssMeetingRegistrationResponses::class)
            ->middleware('signed')
            ->name('qsyss-meeting.responses');

        Route::get('survey/{survey}/{slug?}', EntryForm::class);

        Route::post(uri: 'clicksend/webhook', action: ClicksendWebhookController::class);

        Route::get(uri: 'checkout/success', action: [CheckoutController::class, 'success'])
            ->name(name: 'checkout.success');

        Route::get(uri: 'checkout/cancel', action: [CheckoutController::class, 'cancel'])
            ->name(name: 'checkout.cancel');

        foreach (AwsSnsController::$routes as $routeKey => $route) {
            $routeName = 'ses.notification.' . $routeKey;

            $controllerActionName = Str::camel($routeKey);

            Route::post('ses/notification/' . $route, [AwsSnsController::class, $controllerActionName])->name($routeName);
        }

        Route::get('meet/{meeting}/resolutions/detailed-result', function (Meeting $meeting) {
            return app(GenerateDetailedResultPdf::class)->execute($meeting);
        });
    }
);
