<?php

namespace App\Libraries\FieldsImport
{
    use App\Exceptions;
    
    /**
     * Email field importing from Excel
     */
    class FieldImport_email extends FieldImport
    {
        
        /**
         * Sets field value
         */
        public function prepareVal()
        {
            $val = trim($this->excel_value);
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    throw new Exceptions\DXCustomException(trans('errors.import_wrong_email', ['field' => $this->fld->title_form, 'val' => $val]));
                }
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}