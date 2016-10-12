<?php

namespace App\Libraries\Workflows\Performers
{   
    use DB;
    
    /**
     * Darbplūsmas uzdevuma izpildītāja abstraktā klase     *
     */
    abstract class Performer
    {

        /**
         * Darbplūsmas soļa objekts tabulas dx_workflows rinda
         * @var type 
         */
        public $step_row = null;

        /**
         * Dokumenta ID
         * @var integer 
         */
        public $item_id = 0;        
                
        /**
         * Masīvs ar darbiniekiem un to uzdevumu izpildes termiņiem
         * 
         * @var array 
         */
        public $employees = [];
        
        /**
         * Manuālās saskaņošanas gadījumā - uzsāktās darbplūsmas instances ID
         * 
         * @var integer 
         */
        public $wf_info_id = 0;        
               
        /**
         * Atgriež darbplūsmas soļa izpildītāja (darbinieka) ID
         */
        abstract protected function setEmployeeID();

        /**
         * Izpildītāja klases konstruktors
         *
         * @param  string $step_row Darbplūsmas soļa objekts
         * @param  ineger $item_id  Ieraksta (dokumenta) ID
         * @return void
         */
        public function __construct($step_row, $item_id, $wf_info_id)
        {
            $this->step_row = $step_row;
            $this->item_id = $item_id;
            $this->wf_info_id = $wf_info_id;
            $this->setEmployeeID();
        }
        
        /**
         * Atgriež darbplūsmas uzdevuma izpildītāju masīvu
         *          * 
         * @return array
         */
        public function getEmployees() {
            
            $arr_empl = [];
            
            foreach($this->employees as $employee) {
                $subst_data = \App\Libraries\Workflows\Helper::getSubstitEmpl($employee["empl_id"], "");
                
                $employee["empl_id"] = $subst_data["employee_id"];
                $employee["subst_data"] = $subst_data;
                
                $employee["due"] = $this->getDueDate($employee["due_days"]); //date("Y-m-d", strtotime("+" . $employee["due_days"] . " days"));
                array_push($arr_empl, $employee);
            }            
            
            return $arr_empl;
        }
        
        /**
         * Nosaka uzdevuma termiņa datumu
         * Ja solim definēts izmantot kā datumu lauku no dokumenta, tad tas ir primārs
         * 
         * @param integer $due_days Dienu skaits termiņam no darbplūsmas soļa
         * @return date Termiņa datums db akceptētā formātā yyyy-mm-dd
         */
        private function getDueDate($due_days) {
            if ($this->step_row->due_field_id) {
                
                $fld_row = DB::table("dx_lists_fields")->where('id', '=', $this->step_row->due_field_id)->first();
            
                \App\Libraries\Workflows\Helper::validateDueField($fld_row);
            
                return \App\Libraries\Workflows\Helper::getDocEmplValue($this->step_row->list_id, $this->item_id, $fld_row);
            
            }
            else {
                return date("Y-m-d", strtotime("+" . $due_days . " weekdays"));
            }
        }

    }

}
