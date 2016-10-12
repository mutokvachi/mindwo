<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Klase apstrādā komandu izpildes grafiku
 */
class Kernel extends ConsoleKernel
{
    /**
     * @var array Artisan komandu saraksts
     */
    protected $commands = [
        \App\Console\Commands\ProcessWorkerCommand::class,
        \App\Console\Commands\CheckQueueListener::class,
        \App\Console\Commands\FixImageSizes::class,
    ];

    /**
     * Definē komandu izpildes grafiku
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sagatavo procesus, kurus nepieciešams izpildīt un pievieno izpildāmo Laravel darbu rindai
        $schedule->command('process_worker_command')
                ->everyMinute();
                

        // Izpilda Laravel darbus no rindas
        $schedule->command('queue:listen')
                ->everyMinute();
    }
}