<?php

namespace App\Libraries\FieldsImport
{
    use App\Exceptions;
    
    /**
     * Field importing from Excel - date
     */
    class FieldImport_date extends FieldImport
    {
        /**
         * Expected date format
         */
        const DATE_FORMAT = "yyyy-mm-dd";
        
        /**
         * Sets field value
         */
        public function prepareVal()
        {
            $val = $this->excel_value;
            if (strlen($val) == 0)
            {
                $val == null;
            }
            else
            {
                $date = check_date($val, self::DATE_FORMAT);
            
                if (strlen($date) == 0)
                {
                    throw new Exceptions\DXCustomException(sprintf(trans('errors.import_wrong_date'), $this->fld->title_form, $val, self::DATE_FORMAT));
                }
            
                $val = $date;
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}