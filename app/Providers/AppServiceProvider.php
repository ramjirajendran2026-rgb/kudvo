<?php

namespace App\Providers;

use App\Filament\Election\Http\Responses\Auth\LogoutResponse;
use App\KudvoManager;
use App\Models\User;
use App\Services\Clicksend\ClicksendChannel;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use App\Settings\ServiceConfig;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormsAction;
use Filament\Forms\Components\Contracts\CanBeLengthConstrained;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use GuzzleHttp\Client;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(abstract: 'kudvo', concrete: function (): KudvoManager {
            return new KudvoManager();
        });

        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::isLocal()) {
            Mail::alwaysTo(address: 'iliyas.m@inodesys.com');
        }

        Gate::define('viewPulse', function (User $user) {
            return $user->canAccessPanel(panel: Filament::getPanel(id: 'admin'));
        });

        Str::macro(name: 'isUnicode', macro: fn ($string): bool => strlen($string) != strlen(utf8_decode($string)));
        Str::macro(name: 'maxLimit', macro: function ($value, $limit = 100, $end = '...'): string {
            if (mb_strwidth($value, 'UTF-8') <= $limit) {
                return $value;
            }

            return rtrim(mb_strimwidth(string: $value, start: 0, width: $limit, trim_marker: $end, encoding: 'UTF-8'));
        });

        Arr::macro(name: 'implodeWithAnd', macro: static function (array $array, string $separator = ', '): string {
            if (empty($array)) {
                return '';
            }

            if (count(value: $array) === 1) {
                return Arr::first($array);
            }

            $lastItem = array_pop($array);

            return implode(separator: $separator, array: $array) . ' and ' . $lastItem;
        });

        Notification::resolved(
            fn (ChannelManager $service) => $service
                ->extend(
                    driver: TwentyFourSevenSmsChannel::NAME,
                    callback: fn () => app(abstract: TwentyFourSevenSmsChannel::class)
                )
        );
        Notification::resolved(
            fn (ChannelManager $service) => $service
                ->extend(
                    driver: ClicksendChannel::NAME,
                    callback: fn () => app(abstract: ClicksendChannel::class)
                )
        );

        $this->app->when(concrete: ClicksendChannel::class)
            ->needs(abstract: SMSApi::class)
            ->give(implementation: function () {
                $serviceConfig = app(abstract: ServiceConfig::class);

                return new SMSApi(
                    client: new Client(),
                    config: Configuration::getDefaultConfiguration()
                        ->setUsername($serviceConfig->clicksend->username)
                        ->setPassword($serviceConfig->clicksend->api_key)
                );
            });

        $supportedLocales = config(key: 'laravellocalization.supportedLocales');
        if (count($supportedLocales) > 1) {
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) use ($supportedLocales) {
                $switch
                    ->excludes(excludes: ['election'])
                    ->locales(array_keys($supportedLocales));
            });
        }

        FilamentColor::register(colors: [
            'primary' => [
                50 => '#effefb',
                100 => '#c8fff4',
                200 => '#91feea',
                300 => '#51f7df',
                400 => '#1ee3cc',
                500 => '#05c7b3',
                600 => '#01b2a4',
                700 => '#067f77',
                800 => '#0a6560',
                900 => '#0e5350',
                950 => '#003332',
            ],
        ]);

        Notifications::alignment(alignment: Alignment::Center);
        Notifications::verticalAlignment(alignment: VerticalAlignment::Start);

        Textarea::configureUsing(
            modifyUsing: fn (Textarea $component) => $component
                ->autosize()
        );
        Field::macro(
            name: 'charCounter',
            macro: function (int $count = 60) {
                $this->helperText(
                    text: function (CanBeLengthConstrained $component) use ($count) {
                        $liveCount = '$wire.' . $component->getStatePath();

                        return new HtmlString(
                            html: <<<HTML
<div
    x-data="{get liveCount() { return $liveCount != null ? $liveCount.length : 0 }}"
    class="text-end"
    :class="liveCount > $count ? 'text-danger-600 dark:text-danger-400' : null"
    x-text="liveCount+'/$count'"
>
</div>
HTML
                        );
                    }
                );

                return $this;
            }
        );

        Table::$defaultDateTimeDisplayFormat = 'M j, Y h:i:s A';
        Table::$defaultDateDisplayFormat = 'M j, Y';
        Table::$defaultTimeDisplayFormat = 'h:i A';

        Action::configureUsing(modifyUsing: function (Action $action) {
            $action->closeModalByClickingAway(condition: false);
        });
        FormsAction::configureUsing(modifyUsing: function (FormsAction $action) {
            $action->closeModalByClickingAway(condition: false);
        });
        InfolistAction::configureUsing(modifyUsing: function (InfolistAction $action) {
            $action->closeModalByClickingAway(condition: false);
        });
        TableAction::configureUsing(modifyUsing: function (TableAction $action) {
            $action->closeModalByClickingAway(condition: false);
        });

        Column::configureUsing(modifyUsing: function (Column $component) {
            $component->wrapHeader();
        });

        CreateAction::configureUsing(modifyUsing: function (CreateAction $action) {
            $action->icon(icon: 'heroicon-m-plus');
        });

        TableCreateAction::configureUsing(modifyUsing: function (TableCreateAction $action) {
            $action->icon(icon: 'heroicon-m-plus');
        });

        Turnstile::configureUsing(modifyUsing: function (Turnstile $component) {
            $component->language(language: 'en-us')
                ->hidden(condition: App::isLocal());
        });
    }
}
