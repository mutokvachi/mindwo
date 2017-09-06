<?php

namespace App\Libraries\FormsActions
{
    use DB;
    use \App\Exceptions;
    
    /**
     * If cancel_time entered then this action will reset statuses fields
     * Action must be called after save
     */
    class Action_EDU_CANCEL_GROUP extends Action
    {  
        /**
         * Minimum characters for cancel reason
         */
        const CANCEL_REASON_MIN_LEN = 10;

        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            $this->db_table_name = ["edu_subjects_groups"];
        }
        
        /**
         * Performs action
         */
        public function performAction()
        {
            $group_id = $this->request->input('item_id', 0);
            
            if (!$group_id) {
                return; // we can cancel only existing group and not newly added
            }

            $this->validateReason();

            $group = DB::table('edu_subjects_groups')
                    ->where('id', '=', $group_id)
                    ->first();

            $cancel_date = trim($this->request->input('canceled_time', ''));
            if ($cancel_date && ($group->is_published || $group->is_complecting)) {
                DB::table('edu_subjects_groups')
                ->where('id', '=', $group->id)
                ->update([
                    'is_published' => 0,
                    'is_complecting' => 0
                ]);
            }
        }

        /**
         * Validates cancelation reason
         * @return void
         */
        private function validateReason() {
            $cancel_date = trim($this->request->input('canceled_time', ''));
            $cancel_reason = trim($this->request->input('canceled_reason', ''));

            if (!$cancel_date) {
                if (!$cancel_reason) {
                    return;
                }

                throw new Exceptions\DXCustomException(trans('edu_errors.cancel_date_not_set'));
            }

            if (strlen($cancel_reason) < self::CANCEL_REASON_MIN_LEN) {
                throw new Exceptions\DXCustomException(trans('edu_errors.cancel_reason_not_set', ['min' => self::CANCEL_REASON_MIN_LEN]));
            }
        }

    }

}