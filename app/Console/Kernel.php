<?php

namespace App\Console;

use App\Console\Commands\Monitoring\FailedJobsCommand;
use App\Console\Commands\SendDomainsToQueueCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(FailedJobsCommand::class)->hourly();
        $schedule->command(SendDomainsToQueueCommand::class)
            ->days([Schedule::WEDNESDAY, Schedule::SATURDAY])
            ->at('01:00')
            ->runInBackground()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
