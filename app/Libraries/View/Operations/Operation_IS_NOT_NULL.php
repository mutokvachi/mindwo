<?php

namespace App\Libraries\View\Operations
{

    /**
     * Makes WHERE SQL for field operation - IS NOT NULL
     */
    class Operation_IS_NOT_NULL extends Operation
    {

        /**
         * Returns WHRE SQL for field operation
         */
        public function getWhereSQL()
        {
            return " AND " . $this->field_where_name . " IS NOT NULL";
        }

    }

}