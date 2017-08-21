<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if teachers dont overlap for 1 lesson
     */
    class Validator_TEACHER_TIME_OVERLAP extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $teachers = DB::table('edu_subjects_groups_days_teachers')
                            ->where('group_day_id', '=', $day->id)
                            ->orderBy('time_from')
                            ->get();
                                
                $end = null;
                
                foreach($teachers as $teacher) {
                    if (!$end) {                        
                        $end = $teacher->time_to;
                    }
                    else {
                        if ($teacher->time_from < $end) {
                            if (!$list) {
                                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days');
                            }
                            $this->setError($list->id, $day->id, $day->title);
                            break;
                        }
                        $end = $teacher->time_to;
                    }
                }
            }
        }
    }

}