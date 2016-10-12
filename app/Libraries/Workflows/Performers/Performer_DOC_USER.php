<?php

namespace App\Libraries\Workflows\Performers
{
    use DB;
    use App\Exceptions;
    
    /**
     * Darbplūsmas izpildītājs - darbinieks no dokumenta (lauka)
     */
    class Performer_DOC_USER extends Performer
    {

        /**
         * Dokumenta izveidotājs (no tabulas dx_field_represent)
         */
        const REPRESENT_CREATOR = 8;

        /**
         * Uzstāda darbinieka ID
         */
        public function setEmployeeID()
        {
            $fld_row = DB::table("dx_lists_fields")->where('id', '=', $this->step_row->field_id)->first();
            
            \App\Libraries\Workflows\Helper::validateEmplField($fld_row);
            
            $employee_id = \App\Libraries\Workflows\Helper::getDocEmplValue($this->step_row->list_id, $this->item_id, $fld_row);
            
            $item = ["empl_id" => $employee_id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
            array_push($this->employees, $item);
        }

    }

}