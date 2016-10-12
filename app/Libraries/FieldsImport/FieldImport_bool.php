<?php

namespace App\Libraries\FieldsImport
{
    use App\Exceptions;
    
    /**
     * Field importing from Excel - boolean
     */
    class FieldImport_bool extends FieldImport
    {
        
        /**
         * Sets field value
         */
        public function prepareVal()
        {
            $val = $this->excel_value;
            if (strlen($val) == 0)
            {
                if (strlen($this->fld->default_value) > 0) {
                    $val = $this->fld->default_value;
                }
            }
            else
            {
                if ($val == trans('fields.yes')) {
                    $val = 1;
                }
                else if ($val == trans('fields.no')) {
                    $val = 0;
                }
                else {
                    throw new Exceptions\DXCustomException(sprintf(trans('errors.import_wrong_bool'), $this->fld->title_form, $this->excel_value, trans('fields.yes'), trans('fields.no')));
                }
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}