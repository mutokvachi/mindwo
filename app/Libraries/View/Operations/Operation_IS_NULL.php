<?php

namespace App\Libraries\View\Operations
{
    /**
     * Makes WHERE SQL for field operation - IS NULL
     */
    class Operation_IS_NULL extends Operation
    {
        /**
         * Returns WHRE SQL for field operation
         */
        public function getWhereSQL()
        {
            return " AND " . $this->field_where_name . " IS NULL";
                                                
        }
    }

}