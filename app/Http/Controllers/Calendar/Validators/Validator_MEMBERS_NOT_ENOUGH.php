<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if grop have enough members to be published (for inner groups only
     */
    class Validator_MEMBERS_NOT_ENOUGH extends Validator
    {
        public function validateGroup() {
            
            if (!$this->group->is_inner_group) {
                return; // we control members count only for inner groups
            }
            
            $member_count = DB::table('edu_subjects_groups_members')
                    ->where('group_id', '=', $this->group->id)
                    ->count();            
            
            $percent = ($this->group->seats_limit > 0) ? round($member_count/$this->group->seats_limit*100,0) : 0;
                        
            if ($percent < 50) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }            
        }
    }

}