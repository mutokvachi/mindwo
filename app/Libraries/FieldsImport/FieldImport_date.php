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
        const DATE_FORMAT1 = "dd.mm.yyyy";
        const DATE_FORMAT2 = "yyyy-mm-dd";
        
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
                $date = check_date($val, self::DATE_FORMAT1);
            
                if (strlen($date) == 0)
                {
                    $date = check_date($val, self::DATE_FORMAT2);
                    
                    if (strlen($date) == 0) {
                        throw new Exceptions\DXCustomException(sprintf(trans('errors.import_wrong_date'), $this->fld->title_form, $val, self::DATE_FORMAT1));
                    }
                }
                
                $val = $date;
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}