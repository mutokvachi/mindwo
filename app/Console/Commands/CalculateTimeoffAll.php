<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use File;
use App\Exceptions;
use App\Libraries\Timeoff\Timeoff;

/**
 * Calculates time off for all employees
 */
class CalculateTimeoffAll extends Command
{
    
    protected $signature = 'mindwo:timeoff_all {timeoff_id}';
    
    protected $description = 'Calculates time off for all employees';
        
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
        $this->info("Time off type ID: " . $this->argument('timeoff_id'));
        
        $users = $this->getEmployeesIDs();
                
        foreach($users as $user) {
            try {
                $timeoff = new Timeoff($user->id, $this->argument('timeoff_id'));
                $timeoff->is_system_process = true;
                $timeoff->calculate();
                $this->info('Time off calculated for employee with ID ' . $user->id);
            }
            catch(Exceptions\DXCustomException $e)
            {
                $err = "Employee id " . $user->id . " error: " . $e->getMessage();
                $this->warn($err);
                \Log::info($err);
            }
        }
        
        $this->info('Time off calculation done!');
    }
    
    /**
     * Get all employees ID's
     * @return Array
     */
    private function getEmployeesIDs() {
        return DB::table('dx_users')->select('id')->get();
    }
    
         
    
}