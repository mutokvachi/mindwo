<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups lessons have at least 1 teacher
     */
    class Validator_NO_TEACHER extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $teacher = DB::table('edu_subjects_groups_days_teachers')
                            ->where('group_day_id', '=', $day->id)
                            ->first();
                
                if (!$teacher) {
                    if (!$list) {
                        $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days');
                    }
                    $this->setError($list->id, $day->id, $day->title);
                }
            }
        }
    }

}