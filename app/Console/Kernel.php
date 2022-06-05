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
        Commands\FiveHourAppointmentCron::class,
        Commands\TwoHourAppointmentCron::class,
        Commands\OneDayAppointmentCron::class,
        Commands\TwoDaysAppointmentCron::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fivehour:appointment')->everyMinute();
        $schedule->command('twohour:appointment')->everyMinute();
        $schedule->command('oneday:appointment')->dailyAt('11:00');
        $schedule->command('twoday:appointment')->dailyAt('10:00');
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
