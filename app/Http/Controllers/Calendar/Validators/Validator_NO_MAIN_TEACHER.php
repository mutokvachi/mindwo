<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if group have main teacher provided
     */
    class Validator_NO_MAIN_TEACHER extends Validator
    {
        public function validateGroup() {
            
            if (!$this->group->main_teacher_id) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }                  
        }
    }

}