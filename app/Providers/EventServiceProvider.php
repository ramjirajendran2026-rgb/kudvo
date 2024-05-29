<?php

namespace App\Providers;

use App\Events\NomineeNominated;
use App\Listeners\Auth\NotifyEmailVerified;
use App\Listeners\DestroyBrowserSession;
use App\Listeners\LogClicksendSentSms;
use App\Listeners\LogSentNotification;
use App\Listeners\LogSentMail;
use App\Listeners\LogTwentyFourSevenSmsSentMessage;
use App\Listeners\SendNomineeNominatedNotifications;
use App\Services\Clicksend\SmsSent;
use App\Services\TwentyFourSevenSms\SmsMessageSent;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationSent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CurrentDeviceLogout::class => [
            DestroyBrowserSession::class,
        ],
        Logout::class => [
            DestroyBrowserSession::class,
        ],
        NomineeNominated::class => [
            SendNomineeNominatedNotifications::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NotificationSent::class => [
            LogSentNotification::class,
        ],
        SmsMessageSent::class => [
            LogTwentyFourSevenSmsSentMessage::class,
        ],
        SmsSent::class => [
            LogClicksendSentSms::class,
        ],
        MessageSent::class => [
            LogSentMail::class,
        ],
        Verified::class => [
            NotifyEmailVerified::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
