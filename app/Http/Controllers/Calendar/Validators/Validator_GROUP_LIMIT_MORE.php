<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups seats limits is not bigger than rooms limits
     */
    class Validator_GROUP_LIMIT_MORE extends Validator
    {

        public function validateGroup() {
            $days = DB::table('edu_subjects_groups_days as d')
                    ->select('d.id', 'd.title', 'r.room_limit')
                    ->join('edu_rooms as r', 'd.room_id', '=', 'r.id')
                    ->where('d.group_id', '=', $this->group->id)
                    ->get();
            
            $list = null;
            foreach($days as $day) {
                if ($this->group->seats_limit > $day->room_limit) {
                    if (!$list) {
                        $list = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days');
                    }
                    $this->setError($list->id, $day->id, $day->title);
                }
            }            
        }
    }

}