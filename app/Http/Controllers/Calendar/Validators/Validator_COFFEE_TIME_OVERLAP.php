<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if there are no coffee pauses in the same room as gorup lessons at the same time
     */
    class Validator_COFFEE_TIME_OVERLAP extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $other_gr = DB::table('edu_subjects_groups_days_pauses as p')
                            ->select('p.time_from', 'p.time_to')
                            ->join('edu_subjects_groups_days as d', 'p.group_day_id', '=', 'd.id')
                            ->where('d.group_id', '!=', $this->group->id)
                            ->whereDate('d.lesson_date', '=', $day->lesson_date)
                            ->where('p.room_id', '=', $day->room_id)
                            ->orderBy('p.time_from')
                            ->get();
                
                foreach($other_gr as $o_gr) {                    
                    if ($day->time_to > $o_gr->time_from && $day->time_from < $o_gr->time_to) {                        
                        $this->setError(0, 0, trans('db_edu_publish_validators.err_action_edit_calendar') . ' ' . short_date($day->lesson_date), 'calendar/scheduler/' . $day->room_id . '?current_date=' . $day->lesson_date); 
                    }
                }
            }
        }
    }

}