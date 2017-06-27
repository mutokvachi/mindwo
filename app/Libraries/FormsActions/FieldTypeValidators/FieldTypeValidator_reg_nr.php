<?php

namespace App\Libraries\FormsActions\FieldTypeValidators
{
    use \App\Exceptions;

    /**
     * Field validator - reg_nr
     */
    class FieldTypeValidator_reg_nr extends FieldTypeValidator
    {        
        /**
         * Validates field
         */
        public function validateField()
        {
            $this->validateMinLenght();
            $this->validateMaxLenght();
            $this->validateNumerator();
        }
        
        /**
         * Validates max_lenght field - value must be greater than 0
         * @throws Exceptions\DXCustomException
         */
        private function validateMinLenght() {
            $length = $this->request->input('max_lenght', 0);
           
            if ($length == 0) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_hint_len', ['ok_len' => 1]));
            }
        }
        
        /**
         * Validates max_lenght field - maximum value
         * 
         * @throws Exceptions\DXCustomException
         */
        private function validateMaxLenght() {
            
            if ($this->fld->max_length == 0) {
                return; // no max length validation
            }
            
            $max_length = $this->request->input('max_lenght', 0);
            
            if ($max_length > $this->fld->max_length) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_max_err', ['max_len' => $this->fld->max_length]));
            }
        }

        /**
         * Checks if there is a numerator assigned to registration number field
         * 
         * @throws Exceptions\DXCustomException
         */
        private function validateNumerator() {
            $num_id = $this->request->input('numerator_id', 0);
            
            if (!$num_id) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_numerator_err'));
            }
        }
    }

}