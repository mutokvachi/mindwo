<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if there are not other groups in the same time in rooms
     */
    class Validator_GROUP_TIME_OVERLAP extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $other_gr = DB::table('edu_subjects_groups_days as d')
                            ->where('d.group_id', '!=', $this->group->id)
                            ->whereDate('d.lesson_date', '=', $day->lesson_date)
                            ->where('d.room_id', '=', $day->room_id)
                            ->orderBy('d.time_from')
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