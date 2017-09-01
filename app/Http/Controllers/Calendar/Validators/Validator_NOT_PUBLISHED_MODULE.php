<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups have published modules
     */
    class Validator_NOT_PUBLISHED_MODULE extends Validator
    {
        public function validateGroup() {
            $module = DB::table('edu_subjects as s')
                    ->select('m.is_published', 'm.id')
                    ->join('edu_modules as m', 's.module_id', '=', 'm.id')
                    ->where('s.id', '=', $this->group->subject_id)
                    ->first();
            
            if (!$module->is_published) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_modules');
                $this->setError($list->id, $module->id, trans('db_edu_publish_validators.err_action_edit_module'));
            }
        }
    }

}