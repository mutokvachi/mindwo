<?php

namespace App\Libraries\FormsActions
{
    use DB;
    use \App\Exceptions;
    
    /**
     * Validates organization employees quota to add in education group - before saving transaction
     * This action is designed for using on table edu_subjects_groups_orgs
     * 
     * Group field - group_id
     * Organization field - org_id
     * Quota field - places_quota
     */
    class Action_VALIDATE_ORG_QUOTA extends Action
    {        
        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            $this->db_table_name = ["edu_subjects_groups_orgs"];
        }
        
        /**
         * Performs action
         */
        public function performAction()
        {
            $group_id = $this->request->input('group_id', 0);
            $org_id = $this->request->input('org_id', 0);
            $places_quota = $this->request->input('places_quota', 0);
            
            if ($places_quota == 0) {
                throw new Exceptions\DXCustomException(trans('edu_errors.quota_not_set'));
            }
            
            $group = DB::table('edu_subjects_groups')
                    ->where('id', '=', $group_id)
                    ->first();
            /*
            if ($places_quota > $group->seats_limit) {
                throw new Exceptions\DXCustomException(trans('edu_errors.quota_exceeded', ['limit' => $group->seats_limit]));
            }
            */
            
            $total = DB::table('edu_subjects_groups_orgs')                    
                    ->where('group_id', '=', $group_id)
                    ->where('org_id', '!=', $org_id)
                    ->sum('places_quota');
            
            if (($places_quota + $total) > $group->seats_limit) {
                $left = $group->seats_limit - $total;
                throw new Exceptions\DXCustomException(trans('edu_errors.quota_exceeded', ['limit' => $group->seats_limit, 'left' => $left]));
            }
        }

    }

}