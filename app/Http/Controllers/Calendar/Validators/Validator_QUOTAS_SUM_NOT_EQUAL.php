<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if institutions quotas summ is equal to group limit
     */
    class Validator_QUOTAS_SUM_NOT_EQUAL extends Validator
    {
        public function validateGroup() {
            
            if (!$this->group->is_inner_group) {
                return; // we calculate quotas only for inner groups
            }
            
            $org_sum = DB::table('edu_subjects_groups_orgs')
                    ->where('group_id', '=', $this->group->id)
                    ->sum('places_quota');
            
            if ($org_sum != $this->group->seats_limit) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups');                    
                $this->setError($list->id, $this->group->id, trans('db_edu_publish_validators.err_action_edit_group'));
            }            
        }
    }

}