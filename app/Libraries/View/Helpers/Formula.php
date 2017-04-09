<?php

namespace App\Libraries\View\Helpers
{
    use DB;
    
    /**
     * Returns field definition if it is formula
     */
    class Formula
    {        
        /**
         * Array with field parameters
         * @var Array 
         */
        private $field_row = null;
        
        /**
         * Class constructor
         * @param array $field_row Field parameters array
         */
        public function __construct($field_row)
        {            
            $this->field_row = $field_row;         
        }
        
        /**
         * Returns formula field in SQL format
         * 
         * @return string
         */
        public function getFieldFormula() {   
            $formula = $this->field_row->formula;
            
            if (strlen($formula) == 0) {
                return "";
            }
		
            $out_arr = [];
            preg_match_all('/\[(.*?)\]/', $formula, $out_arr);
            
            $qMarks = str_repeat('?,', count($out_arr[1]) - 1) . '?';
            $sql_f = "SELECT db_name, title_list from dx_lists_fields WHERE list_id = " . $this->field_row->list_id . " AND title_list in (" . $qMarks  . ")";

            $rows_formulas = DB::select($sql_f, $out_arr[1]);

            foreach($rows_formulas as $row_f)
            {
                $formula = str_replace("[" . $row_f->title_list . "]", $this->field_row->list_table_name . "." . $row_f->db_name, $formula);
            }
            
            return $formula;
        }
    }

}