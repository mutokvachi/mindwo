<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups have published programms
     */
    class Validator_NOT_PUBLISHED_PROGRAMM extends Validator
    {
        public function validateGroup() {
            $prog = DB::table('edu_subjects as s')
                    ->select('p.is_published', 'p.id')
                    ->join('edu_modules as m', 's.module_id', '=', 'm.id')
                    ->join('edu_programms as p', 'm.programm_id', '=', 'p.id')
                    ->where('s.id', '=', $this->group->subject_id)
                    ->first();
            
            if (!$prog->is_published) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_programms');
                $this->setError($list->id, $prog->id, trans('db_edu_publish_validators.err_action_edit_programm'));
            }
        }
    }

}