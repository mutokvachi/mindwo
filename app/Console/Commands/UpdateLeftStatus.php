<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Config;

/**
 * Update all employe left status (holidays, vacations, sick etc)
 */
class UpdateLeftStatus extends Command
{
    
    protected $signature = 'mindwo:update_left';
    
    protected $description = 'Update all employe left status (holidays, vacations, sick etc)';
    
    /**
     * Current date
     * 
     * @var string 
     */
    private $now = null;
    
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
        $this->now = Carbon::now(Config::get('dx.time_zone'));

        $this->updateLeaves();
     
        $this->UpdateHolidays();
        
        $this->info('Left status update done!');
    } 
    
    /**
     * Updates dx_users table with employees leaves info (holidays)
     */
    private function UpdateHolidays() {
        $holidays = \App\Libraries\Helper::getHolidaysArray(false);
        
        for ($i = 0; $i<count($holidays); $i++) {
            if ($this->now->between(Carbon::createFromFormat("Y-m-d",$holidays[$i]['date_from']), Carbon::createFromFormat("Y-m-d",$holidays[$i]['date_to']))) {
                DB::table('dx_users')
                    ->whereNull('left_to')
                    ->where(function($query) use ($holidays, $i){
                        if ($holidays[$i]['country_id']) {
                            $query->where('doc_country_id', '=', $holidays[$i]['country_id']);
                        }
                    })
                    ->update([
                        'left_from' => $holidays[$i]['date_from'],
                        'left_to' => $holidays[$i]['date_to'],
                        'left_holiday_id' => $holidays[$i]['holiday_id']
                    ]);
            }
        }
    }
    
    /**
     * Updates dx_users table with employees leaves info (vacations/sick)
     */
    private function updateLeaves() {
        $lefts = $this->getLefts();

        foreach($lefts as $left) {
            DB::table('dx_users')->where('id', '=', $left->user_id)->update([
                'left_from' => $left->left_from,
                'left_to' => $left->left_to,
                'left_reason_id' => $left->left_reason_id,
                'substit_empl_id' => $left->substit_empl_id,
                'left_holiday_id' => null
            ]);
        }
    }
    
    /**
     * Get array with employees leaves current status for today
     * @return array
     */
    private function getLefts() {
        return  DB::table('dx_users as u')
                ->select(
                        'u.id as user_id',
                        'l.left_from',
                        'l.left_to',
                        'l.left_reason_id',
                        'l.substit_empl_id'
                        )
                ->leftJoin('dx_users_left as l', function($join) {
                       $join->on('l.user_id', '=', 'u.id')
                            ->where('l.left_from', '<=', $this->now->toDateString())
                            ->where('l.left_to', '>=', $this->now->toDateString());
                })
                ->get();
    }
}