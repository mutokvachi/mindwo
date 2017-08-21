<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    use Carbon\Carbon;
    
    /**
     * Validates if teachers total time covers lesson time
     */
    class Validator_TEACHER_TIME_NOT_COVER extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $day_from = Carbon::createFromFormat('Y-m-d H:i:s',$day->lesson_date . ' ' . $day->time_from);
                $day_to = Carbon::createFromFormat('Y-m-d H:i:s',$day->lesson_date . ' ' . $day->time_to);
                $day_total = $day_from->diffInHours($day_to);   
                
                $teachers = DB::table('edu_subjects_groups_days_teachers')
                            ->where('group_day_id', '=', $day->id)
                            ->orderBy('time_from')
                            ->get();
                                
                $end = null;
                
                $teach_total = 0;
                foreach($teachers as $teacher) {
                    $teach_from = Carbon::createFromFormat('Y-m-d H:i:s',$day->lesson_date . ' ' . $teacher->time_from);
                    $teach_to = Carbon::createFromFormat('Y-m-d H:i:s',$day->lesson_date . ' ' . $teacher->time_to);
                    
                    $teach_total = $teach_total + $teach_from->diffInHours($teach_to);   
                }
                
                if ($day_total!=$teach_total) {
                    if (!$list) {
                        $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days');
                    }
                    $this->setError($list->id, $day->id, $day->title);
                }
            }
        }
    }

}