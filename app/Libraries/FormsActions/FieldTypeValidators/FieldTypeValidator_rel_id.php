<?php

namespace App\Libraries\FormsActions\FieldTypeValidators
{
    use \App\Exceptions;

    /**
     * Field validator - rel_id
     */
    class FieldTypeValidator_rel_id extends FieldTypeValidator
    {
        /**
         * Validates field
         */
        public function validateField()
        {
            $this->validateRelTable();
        }
        
        /**
         * Validates lookup/autocompleate related register db table
         */
        private function validateRelTable() {
            if (!$this->fld->rel_table_name) {
                return; // no related table validation
            }
            
            $rel_list_id = $this->request->input('rel_list_id', 0);
            $rel_field_id = $this->request->input('rel_display_field_id', 0);
            
            if (!$rel_list_id) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_no_rel_list'));
            }
            
            if (!$rel_field_id) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_no_rel_field'));                
            }
            
            $obj = \App\Libraries\DBHelper::getListObject($rel_list_id);
            
            if ($obj->db_name != $this->fld->rel_table_name) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_wrong_rel_list'));
            }
        }
    }

}