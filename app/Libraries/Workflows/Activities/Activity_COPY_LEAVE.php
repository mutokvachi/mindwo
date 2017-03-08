<?php

namespace App\Libraries\Workflows\Activities
{
    use DB;
    
    /**
     * Copy approved timeoff request info to leaves register
     */
    class Activity_COPY_LEAVE extends Activity
    {
        /**
         * Performs custom activity - copy timeoff info to leaves register
         */
        public function performActivity()
        {
            $timeoff = DB::table('dx_timeoff_requests')->where('id', '=', $this->item_id)->first();
            
            // keep in mind that on table dx_users_left is trigger which updated dx_users table with left info
            DB::table('dx_users_left')->insert([
                'user_id' => $timeoff->user_id,
                'left_from' => $timeoff->from_date,
                'left_to' => $timeoff->to_date,
                'left_reason_id' => $timeoff->timeoff_type_id
            ]);
            
            return true;
        }

    }

}