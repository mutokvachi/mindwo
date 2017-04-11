<?php

namespace App\Libraries\View\Helpers
{
    use DB;
    use Request;
    use Config;
    
    /**
     * Returns WHERE SQL part for reports date interval filtering
     */
    class ReportFiltering
    {        
        /**
         * Returns WHERE SQL part for reports date interval filtering
         * 
         * @return string
         */
        public function getWhereSQL() {            
            $filter_field_id = Request::input('dx_filter_field_id', 0);
            
            if (!$filter_field_id) {
                return ""; // no filtering
            }
            
            $date_from = $this->getDateVal("dx_filter_date_from");
            $date_to = $this->getDateVal("dx_filter_date_to");
            
            $fld_row = DB::table('dx_lists_fields as lf')
                       ->select('lf.db_name', 'lf.formula', 'lf.list_id', 'o.db_name as list_table_name')
                       ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
                       ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                       ->where('lf.id', '=', $filter_field_id)->first();
            
            $field_name = $fld_row->db_name;
            if ($fld_row->formula) {
                $formula_obj = new Formula($fld_row);
                $field_name = $formula_obj->getFieldFormula();
            }
            
            $sql = " ";
            if ($date_from) {
                $sql .= "AND date(" . $field_name . ") >='" . $date_from . "' ";
            }
            
            if ($date_to) {
                $sql .= "AND date(" . $field_name . ") <='" . $date_to . "' ";
            }
            
            return $sql;
        }
        
        /**
         * Prepares filtering date value in DB format
         * 
         * @param string $filter_name Date HTML input field name
         * @return string Return date in format yyyy-mm-dd
         * @throws Exceptions\DXCustomException
         */
        private function getDateVal($filter_name) {
            
            $val = Request::input($filter_name, '');
            
            if (!$val) {
                return "";
            }
            
            $date_format = Config::get('dx.date_format');
            $date = check_date($val, $date_format);
            
            if (strlen($date) == 0)
            {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.wrong_date_format'), $date_format));
            }
            
            return $date;
        }
    }

}