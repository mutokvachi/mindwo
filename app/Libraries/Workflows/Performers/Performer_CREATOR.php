<?php

namespace App\Libraries\Workflows\Performers
{
    use DB;
    use App\Exceptions;
    use Log;
    
    /**
     * Darbplūsmas izpildītājs - dokumenta sagatavotājs klase
     */
    class Performer_CREATOR extends Performer
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
            $table_name = \App\Libraries\Workflows\Helper::getListTableName($this->step_row->list_id);
            
            try {
                // Visās CMS tabulās kurās uzkrāj izmaiņu vēsturi ir lauks created_user_id
                $employee_id = DB::table($table_name)->select('created_user_id')->where('id','=',$this->item_id)->first()->created_user_id;
                
                $item = ["empl_id" => $employee_id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
                array_push($this->employees, $item);                
            }
            catch (\Exception $e) {
                Log::info('Kļūda - nav iespējams noteikt dokumenta izveidotāju! Info: ' . $e->getMessage());
                
                throw new Exceptions\DXCustomException("Nav iespējams noteikt dokumenta izveidotāju!");
            }
        }

    }

}