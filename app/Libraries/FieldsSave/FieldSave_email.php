<?php

namespace App\Libraries\FieldsSave
{
    use App\Exceptions;

    /**
     * Validates and formats email
     */
    class FieldSave_email extends FieldSave
    {
        /**
         * Validates and formats field value
         */
        public function prepareVal()
        {
            $val = trim($this->request->input($this->fld->db_name, ''));
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    throw new Exceptions\DXCustomException(trans('errors.not_valid_email', ['email' => $val]));
                }

                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}