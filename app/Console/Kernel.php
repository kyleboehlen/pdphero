<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cloudflare:reload')->daily();
        $schedule->command('free-trial:send-notifications')->dailyAt('16:00');
        $schedule->command('purge:profile-pictures --silent')->daily();
        $schedule->command('mysql:dump')->daily();

        // Notifications
        $schedule->command('check:addiction-milestones')->everyMinute();

        // Reminders
        $schedule->command('reminders:goal-action-items')->everyMinute();
        $schedule->command('reminders:habits')->everyMinute();
        $schedule->command('reminders:to-do')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
