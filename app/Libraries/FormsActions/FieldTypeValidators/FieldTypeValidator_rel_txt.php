<?php

namespace App\Libraries\FormsActions\FieldTypeValidators
{
    use \App\Exceptions;

    /**
     * Field validator - rel_txt
     */
    class FieldTypeValidator_rel_txt extends FieldTypeValidator
    {
        /**
         * Items as string delimited by semicolon - entered by user in CMS form
         * 
         * @var varchar 
         */
        private $items = "";
        
        /**
         * Validates field
         */
        public function validateField()
        {
            $this->items = trim($this->request->input('items', ''));
            
            $this->validateChooseValueRequred();
            
            $this->validateItemMaxLen();
        }
        
        /**
         * Validates choose dropdown items - must be at least 1 character
         */
        private function validateChooseValueRequred() {            
            if (!$this->items) {
                throw new Exceptions\DXCustomException(trans('constructor.valid_rel_txt'));
            }
        }
        
        /**
         * Validates all choose items - min and max lenghts
         * @throws Exceptions\DXCustomException
         */
        private function validateItemMaxLen() {
            $arr = explode(";", $this->items);
            
            foreach($arr as $item) {
                $len = strlen(trim($item));
                if ($len > $this->fld->max_length) {
                    throw new Exceptions\DXCustomException(trans('constructor.valid_rel_txt_item', ['item' => trim($item), 'len' => $len, 'max' => $this->fld->max_length]));
                }
                
                if ($len == 0) {
                    throw new Exceptions\DXCustomException(trans('constructor.valid_rel_txt_item_none'));
                }
            }
        }
    }

}