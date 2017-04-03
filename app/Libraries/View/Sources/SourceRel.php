<?php

namespace App\Libraries\View\Sources
{

    use Config;
    use App\Libraries\Rights;
    
    /**
     * Prepares source parameters for standard related list field
     */
    class SourceRel extends Source
    {

        /**
         * Prepares source parameters
         */
        public function prepareSource()
        {
            $rel_cnt = $this->params['rel_cnt'];
            
            $this->source_table = $this->field_row->rel_table_db_name . "_" . $rel_cnt;
            $this->source_field = $this->field_row->rel_field_db_name;
            
            $this->alias_field_name = $this->source_table . "_" . $this->source_field;
            
            $this->alias_field_select = $this->source_table . "." . $this->source_field . " as " . $this->alias_field_name;
            
            if ($this->field_row->list_id == Config::get('dx.employee_list_id', 0)) {
                // ignore supervision rules for related employees in employees list
                // because we need to see related manager
                $superv_sql = "";
                $join_type = " LEFT JOIN ";
            }
            else {
                $superv_sql = Rights::getSQLSuperviseRights($this->field_row->rel_list_id, $this->source_table);                                
                $join_type = (strlen($superv_sql) > 0) ? " JOIN " : " LEFT JOIN ";
            }

            $this->sql_join = $join_type . $this->field_row->rel_table_db_name . " " . $this->source_table . " ON " . $this->source_table . ".id = " . $this->list_obj_db_name . "." . $this->field_row->db_name . $superv_sql;

        }

    }

}