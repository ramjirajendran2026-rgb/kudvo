<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(command: 'election:blast-ballot-links')
            ->name(description: 'election:blast-ballot-links')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command(command: 'meeting:blast-participation-links')
            ->name(description: 'meeting:blast-participation-links')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
