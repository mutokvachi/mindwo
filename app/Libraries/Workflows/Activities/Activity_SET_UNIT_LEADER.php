<?php

namespace App\Libraries\Workflows\Activities
{
    use DB;
    use App\Exceptions;
    
    /**
     * Detects next unit leader according to company departments hierarchy (multi level)
     * 
     * This activity works only with documents register (based on table dx_docs) where document creator is stored
     * in field perform_empl_id
     */
    class Activity_SET_UNIT_LEADER extends Activity
    {
        /**
         * Performs custom activity
         */
        public function performActivity()
        {
            $doc_row = DB::table('dx_doc')->where('id', '=', $this->item_id)->first();
            
            $current_employee_id = ($doc_row->empl_signer_id) ? $doc_row->empl_signer_id : $doc_row->perform_empl_id;
                        
            $employee_row = DB::table('dx_users')->where('id', '=', $current_employee_id)->first();
            $unit_row = DB::table('in_departments')->where('id', '=', $employee_row->department_id)->first();                        
                
            $unit_id = ($unit_row->parent_id) ? $unit_row->parent_id : $unit_row->id;

            $parent_unit_leader_id = $this->getUnitLeaderRow($unit_id, $employee_row->display_name);
            
            if ($parent_unit_leader_id == $current_employee_id) {
                return false; // Document creator/or last approver is department leader - no more approval needed 
            }

            DB::table('dx_doc')->where('id', '=', $this->item_id)->update([
                'empl_signer_id' => $parent_unit_leader_id
            ]);
            
            return true;
        }
        
        /**
         * Get unit leader
         * 
         * @param integer $unit_id Unit (department) ID
         * @param string $current_empl_name Employee name for which unit is processed
         * @return integer Employee ID which is leader of provided unit  
         * @throws Exceptions\DXCustomException
         */
        private function getUnitLeaderRow($unit_id, $current_empl_name) {
            $parent_unit_leader = DB::table('dx_users')
                                  ->select('id')
                                  ->where('department_id', '=', $unit_id)
                                  ->where('is_leader', '=', 1)
                                  ->whereNull('valid_to')
                                  ->first();

            if (!$parent_unit_leader) {
                throw new Exceptions\DXCustomException(sprintf(trans('workflow.err_cant_get_unit_leader'), $current_empl_name));
            }
            
            return $parent_unit_leader->id;
        }    

    }

}