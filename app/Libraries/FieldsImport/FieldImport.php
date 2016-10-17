<?php

namespace App\Libraries\FieldsImport
{

    use \App\Exceptions;

    /**
     * Field for importing from Excel
     */
    abstract class FieldImport
    {

        /**
         * Indicates if field value is set
         * @var boolean
         */
        public $is_val_set = 0;

        /**
         * Field object from the table dx_lists_fields
         * @var Object
         */
        public $fld = null;

        /**
         * Array with field values (normaly only 1 item in array, but can be field types which have several items, for example, if field type is file)
         * @var Array
         */
        public $val_arr = array();

        /**
         * Field value from Excel cell
         * @var mixed 
         */
        public $excel_value = null;

        /**
         * Directory full path (without slash) where uploaded data file is stored on the server
         * 
         * @var string
         */
        public $tmp_dir = "";
        
        /**
         * Prepares field value
         */
        abstract protected function prepareVal();

        /**
         * Returns array with field values
         *
         * @return Array
         */
        public function getVal()
        {
            return $this->val_arr;
        }

        /**
         * Field importing constructor
         *
         * @param  mixed $excel_value Field value from Excel cell 
         * @param  object $fld Field object
         * @return void
         */
        public function __construct($excel_value, $fld, $tmp_dir)
        {
            $this->excel_value = $excel_value;
            $this->fld = $fld;
            $this->tmp_dir = $tmp_dir;
            
            $this->prepareVal();

            $this->checkRequired();
        }

        /**
         * Validates if required field is set
         * @throws Exceptions\DXCustomException
         */
        private function checkRequired()
        {
            if ($this->fld->is_required == 1 && !$this->is_val_set) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.required_field'), $this->fld->title_list));
            }
        }

    }

}
