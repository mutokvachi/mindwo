<?php

namespace App\Libraries\FieldsImport
{
    use App\Exceptions;
    use Log;
    
    /**
     * Field importing from Excel - date
     */
    class FieldImport_datetime extends FieldImport
    {
        /**
         * Expected date format
         */
        const DATE_FORMAT1 = "dd.mm.yyyy HH:ii";
        const DATE_FORMAT2 = "yyyy-mm-dd HH:ii";
        
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
                        throw new Exceptions\DXCustomException(sprintf(trans('errors.import_wrong_date'), $this->fld->title_form, $val, self::DATE_FORMAT1, self::DATE_FORMAT2));
                    }
                }
                
                $val = $date;
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}