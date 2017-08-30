<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if group not more than gorup limit
     */
    class Validator_MEMBERS_TOO_MUCH extends Validator
    {
        public function validateGroup() {
            
            if (!$this->group->is_inner_group) {
                return; // we control members count only for inner groups
            }
            
            $member_count = DB::table('edu_subjects_groups_members')
                    ->where('group_id', '=', $this->group->id)
                    ->count();            
            
            $percent = ($this->group->seats_limit > 0) ? round($member_count/$this->group->seats_limit*100,0) : 0;
                        
            if ($this->group->seats_limit < $member_count) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }            
        }
    }

}