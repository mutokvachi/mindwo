<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

/**
 * Calculates time off for given employee
 */
class UnlockItems extends Command
{
    
    protected $signature = 'mindwo:unlock';
    
    protected $description = 'Unlocks locked items from table dx_locks';
        
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
        $del  = DB::table('dx_locks')
           ->whereDate('locked_time', '<', Carbon::now()->subMinutes(30)->toDateTimeString())
           ->delete();
        
        $this->info($del . ' items were unlocked!');
    }
    
         
    
}