<?php

namespace App\Libraries\FormsActions
{
    use DB;
    
    /**
     * Validates list field - before saving transaction
     * This action is designed for using on table dx_lists_fields
     * 
     * Department field - source_id
     * Unit field - pages_count
     * Document creator field - perform_empl_id
     */
    class Action_VALIDATE_LIST_FIELD extends Action
    {        
        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            $this->db_table_name = ["dx_lists_fields"];
        }
        
        /**
         * Performs action
         */
        public function performAction()
        {
            $fld = $this->getFieldObj();
            
            if (!$fld) {
                return; // no validation possible, maybe aggregated field (virtual)
            }
            
            FieldTypeValidators\FieldTypeValidatorFactory::build_validator($this->request, $fld);
        }
        
        /**
         * Returns field object parameters
         */
        private function getFieldObj() {
            $field_type_id = $this->request->input('type_id', 0);
            $db_name = $this->request->input('db_name', '');
            $fld_list_id = $this->request->input('list_id', 0);
            
            $obj = \App\Libraries\DBHelper::getListObject($fld_list_id);
            
            return DB::table('dx_tables_fields as tf')
                   ->select('tf.*', 'ft.sys_name')
                   ->join('dx_field_types as ft', 'tf.field_type_id', '=', 'ft.id')
                   ->where('tf.field_name', '=', $db_name)
                   ->where('tf.table_name', '=', $obj->db_name)
                   ->where('tf.field_type_id', '=', $field_type_id)
                   ->first();
        }

    }

}