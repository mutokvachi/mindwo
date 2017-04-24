<?php

namespace App\Libraries\View\Operations
{
    use Auth;
    
    /**
     * Makes WHERE SQL for field operation - default behavior
     */
    class Operation_default extends Operation
    {
        /**
         * Returns WHRE SQL for field operation
         */
        public function getWhereSQL()
        {
            $operation = trim($this->field_row->operation);
            
            if (strlen($operation) == 0) {
                return "";
            }
            
            $crit = $this->field_row->criteria;
            if ($crit == "[ME]")
            {
                    $crit = Auth::user()->id;
            }

            if ($this->field_row->sys_name == "bool") {
                $crit = ($this->field_row->criteria == "'" . trans('fields.yes') . "'") ? 1 : 0;
            }            
            
            return " AND " . $this->field_where_name . " " . $operation . " " . $crit;
                                                
        }
    }

}