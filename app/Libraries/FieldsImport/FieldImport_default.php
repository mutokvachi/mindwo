<?php

namespace App\Libraries\FieldsImport
{
    /**
     * Default class for field importing from Excel
     */
    class FieldImport_default extends FieldImport
    {
        
        /**
         * Sets field value
         */
        public function prepareVal()
        {
            $val = $this->excel_value;
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}