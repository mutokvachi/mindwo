<?php

namespace App\Libraries\View\Operations
{    
    /**
     * Makes WHERE SQL for field operation - LIKE
     */
    class Operation_LIKE extends Operation
    {
        /**
         * Returns WHRE SQL for field operation
         */
        public function getWhereSQL()
        {
            return " AND " . $this->field_where_name . " LIKE '%" . $this->field_row->criteria . "%' ";
                                                
        }
    }

}