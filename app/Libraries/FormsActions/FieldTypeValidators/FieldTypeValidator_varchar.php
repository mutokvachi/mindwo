<?php

namespace App\Libraries\FormsActions\FieldTypeValidators
{
    use \App\Exceptions;

    /**
     * Field validator - varchar
     */
    class FieldTypeValidator_varchar extends FieldTypeValidator
    {
        /**
         * Cryted value starting part lenght (characters) used for cryptography
         */
        const CRYPTO_BUFFER_LENGHT = 32;
        
        /**
         * Validates field
         */
        public function validateField()
        {
            $this->validateMinLenght();
            $this->validateMaxLenght();
        }
        
        /**
         * Validates max_lenght field - value must be greater than 0
         * @throws Exceptions\DXCustomException
         */
        private function validateMinLenght() {
            $length = $this->request->input('max_lenght', 0);
            
            if ($length == 0) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_hint_len', ['max_len' => 1]));
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
            $is_crypted = $this->request->input('is_crypted', 0);
            
            if ($max_length > $this->fld->max_length) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_max_err', ['max_len' => $this->fld->max_length]));
            }
            
            if (!$is_crypted) {
                return;
            }
            
            // crypted field max len formula: (db_len - 32)/4
            $crypt_max_len = floor(($this->fld->max_length - self::CRYPTO_BUFFER_LENGHT)/4);
            
            if ($crypt_max_len < 1) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_cant_crypt'));
            }
            
            if ($max_length > $crypt_max_len) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_max_err', ['max_len' => $crypt_max_len]));
            }
        }

    }

}