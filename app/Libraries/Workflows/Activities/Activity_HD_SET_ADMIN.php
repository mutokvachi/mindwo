<?php

namespace App\Libraries\Workflows\Activities
{
    use DB;
    
    /**
     * HelpDesk logic - set administrator as responsible person
     * This object works only on list based on table dx_hd_requests
     */
    class Activity_HD_SET_ADMIN extends Activity
    {
        /**
         * Performs custom activity
         */
        public function performActivity()
        {
            return $this->setResponsible("resp_admin_id");
        }
        
        /**
         * Finds request type record, reads responsible person and set it as responsible to HelpDesk request item
         * 
         * @param string $fld_name Field name for responsible person (from table dx_hd_request_types)
         * @return boolean TRUE - responsible person is set or FALSE if not set
         */
        protected function setResponsible($fld_name) {
            $hd_request = DB::table('dx_hd_requests')->where('id', '=', $this->item_id)->first();
            
            $hd_type = DB::table('dx_hd_request_types')->where('id', '=', $hd_request->request_type_id)->first();
            
            if (!$hd_type->$fld_name) {
                return false;
            }
            
            DB::table('dx_hd_requests')
                    ->where('id', '=', $this->item_id)
                    ->update(['responsible_empl_id' => $hd_type->$fld_name]);
            
            return true;
        }    

    }

}