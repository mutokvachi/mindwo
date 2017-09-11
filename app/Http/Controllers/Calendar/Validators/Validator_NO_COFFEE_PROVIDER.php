<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if there are coffee pauses without provider company assigned
     */
    class Validator_NO_COFFEE_PROVIDER extends Validator
    {
        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days')
                    ->where('group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            
            foreach($days as $day) {
                $pauses = DB::table('edu_subjects_groups_days_pauses')
                          ->where('group_day_id', '=', $day->id)
                          ->whereNull('feed_org_id')
                          ->get();
                                
                foreach($pauses as $pauses) {                    
                    if (!$list) {
                        $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days_pauses');
                    }
                    $this->setError($list->id, $pauses->id, trans('db_edu_publish_validators.err_action_edit_pause') . ' ' . substr($pauses->time_from, 0, 5) . ' - ' . substr($pauses->time_to, 0, 5));
                }
            }
        }
    }

}