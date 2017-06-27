<?php

namespace App\Libraries\View\Operations
{    
    /**
     * View field operation class
     */
    abstract class Operation
    {
        /**
         * Field name used in WHERE criteria
         * @var string 
         */
        public $field_where_name = "";        
                
        /**
         * Array with view field properties
         * @var Array 
         */
        public $field_row = null;     
               
        /**
         * Gets WHERE SQL part for operation
         */
        abstract protected function getWhereSQL();

        /**
         * Constructor for field operation (WHERE criteria builder)
         * 
         * @param string $field_where_name Field name used in WHERE criteria
         * @param Array $field_row Array with view field properties
         */
        public function __construct($field_where_name, $field_row)
        {
            $this->field_where_name = $field_where_name;            
            $this->field_row = $field_row;
        }
    }

}
