<?php

namespace App\Libraries\Workflows\Performers
{
    use DB;
    use App\Exceptions;
    use Log;
    
    /**
     * Darbplūsmas izpildītājs - darbinieks klase
     */
    class Performer_MANAGER extends Performer
    {

        /**
         * Uzstāda darbinieka ID
         * @throws Exceptions\DXCustomException
         */
        public function setEmployeeID()
        {
            $fld_row = DB::table("dx_lists_fields")->where('id', '=', $this->step_row->field_id)->first();
            
            \App\Libraries\Workflows\Helper::validateEmplField($fld_row);
            
            $empl_id = \App\Libraries\Workflows\Helper::getDocEmplValue($this->step_row->list_id, $this->item_id, $fld_row);
            
            $empl_row = DB::table('dx_users')
                        ->where('id', '=', $empl_id)
                        ->first();
            
            if (!$empl_row->manager_id) {
                throw new Exceptions\DXCustomException("Nav iespējams izveidot uzdevumu, jo darbiniekam '" . $empl_row->display_name . "' nav norādīts tiešais vadītājs!");
            }
            
            $item = ["empl_id" => $empl_row->manager_id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
            array_push($this->employees, $item);
        }

    }

}