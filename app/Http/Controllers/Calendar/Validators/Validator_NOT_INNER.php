<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if group is inner (for complecting)
     */
    class Validator_NOT_INNER extends Validator
    {
        public function validateGroup() {
            
            if (!$this->group->is_inner_group) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }                  
        }
    }

}