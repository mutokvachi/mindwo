<?php

namespace App\Console;

use App\Jobs\SendScheduledEmails;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Config;

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
        \App\Console\Commands\UpdateLeftStatus::class,
    ];

    /**
     * Schedule for JOBs
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)    {                

        // Executes employee left info udpate (vacations, holidays, sick etc)
        $schedule->command('mindwo:update_left')
                //->weekdays()
                ->dailyAt(7)
                ->timezone(Config::get('dx.time_zone'));
        
        // Executes monitoring views at 8:00 AM every day
        $schedule->command('mindwo:audit_view')
                //->weekdays()
                ->dailyAt(8)
                ->timezone(Config::get('dx.time_zone'));
        
        // Checks if queue listener is running. Starts queue listener if not started
        $schedule->command('mindwo:check_listener')
                 ->everyTenMinutes();
        
        // Send scheduled emails
        $schedule->call(function()
		{
			$job = new SendScheduledEmails();
			dispatch($job);
		})->everyFiveMinutes();

        if (Config::get('dx.is_backuping_enabled', false)) {
            // Makes db and files backup at midnight
            $schedule->command('backup:run')
                     ->daily();

            // Clean disk space if too many backups 1 hour after midnight
            $schedule->command('backup:clean')
                     ->dailyAt('1:00');
        }
    }
}