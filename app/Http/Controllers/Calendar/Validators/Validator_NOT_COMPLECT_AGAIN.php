<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if group was allready in complecting
     */
    class Validator_NOT_COMPLECT_AGAIN extends Validator
    {
        public function validateGroup() {
            
            if ($this->group->first_publish) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }                  
        }
    }

}