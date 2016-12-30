<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Config;
use Log;

/**
 * Handles JOBs scheduling and Artisan commands
 */
class Kernel extends ConsoleKernel
{
    /**
     * @var array Custom artisan commands
     */
    protected $commands = [
        \App\Console\Commands\ProcessWorkerCommand::class,
        \App\Console\Commands\CheckQueueListener::class,
        \App\Console\Commands\FixImageSizes::class,
        \App\Console\Commands\CalculateTimeoff::class,
        \App\Console\Commands\AuditViewCounts::class,
    ];

    /**
     * Schedule for JOBs
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)    {                

        // Executes monitoring views at 8:00 AM every day
        $schedule->command('mindwo:audit_view')
                //->weekdays()
                ->daily(8)
                ->timezone(Config::get('dx.time_zone'));
        
        $schedule->call(function () {
            Log::info('Working JOB!');
        })->everyMinute();
    }
}