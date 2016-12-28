<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use File;
use App\Exceptions;
use App\Libraries\Timeoff\Timeoff;

/**
 * Calculates time off for given employee
 */
class CalculateTimeoff extends Command
{
    
    protected $signature = 'mindwo:timeoff {employee_id} {timeoff_id}';
    
    protected $description = 'Calculates time off for given employee';
        
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timeoff = new Timeoff($this->argument('employee_id'), $this->argument('timeoff_id'));
        $timeoff->is_system_process = true;
        $timeoff->calculate();
        
        $this->info('Time off calculation done!');
    }
    
         
    
}