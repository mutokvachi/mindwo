<?php
namespace App\Libraries\FormsActions\FieldTypeValidators {
    
    /**
     * Field type validator
     */
    abstract class FieldTypeValidator
    {        
        /**
         * POST/GET request object
         * @var Request 
         */
        public $request = null;
        
        /**
         * Field type db row
         * 
         * @var Object
         */
        public $fld = null;
        
        /**
         * Validates field
         */
        abstract protected function validateField();
       
        /**
        * Field type validator constructor
        *
        * @param  Request $request POST/GET request object
        * @param Object $fld Field type db row
        * @return void
        */
        public function __construct($request, $fld)
        {            
            $this->request = $request;
            $this->fld = $fld;
            
            $this->validateField();
        }
    }
}
