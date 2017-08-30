<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups have at least 1 lesson (day)
     */
    class Validator_NO_DAY extends Validator
    {

        public function validateGroup() {
            $day = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->first();
            
            if (!$day) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }
        }
    }

}