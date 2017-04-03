<?php

namespace App\Libraries\View\Sources
{    
    /**
     * View field source class
     */
    abstract class Source
    {          
        /**
         * Array with view field properties
         * @var Array 
         */
        public $field_row = null;     
        
        /**
         * Table name for register on which view is build
         * @var string 
         */
        public $list_obj_db_name = "";
        
        /**
         * JOIN SQL part
         * @var string
         */
        public $sql_join = "";
        
        /**
         * Source table name
         * @var string 
         */
        public $source_table = "";
        
        /**
         * Source field name
         * @var string
         */
        public $source_field = "";
        
        /**
         * SQL part for SELECT field alias
         * @var string
         */
        public $alias_field_select = "";
        
        /**
         * Field alias name         * 
         * @var string
         */
        public $alias_field_name = "";
        
        /**
         * Array with additional class parameters
         * @var array 
         */
        protected $params = array();

        /**
         * Get additional class parameter
         * 
         * @param string $key Parameter name
         * @return mixed Returns paramter value or false if parameter does not exists
         */
        public function __get( $key )
        {
            if (!isset($this->params[ $key ])) {
                return false;
            }
            
            return $this->params[ $key ];
        }
        
        /**
         * Set's additional class parameter
         * 
         * @param string $key Parameter
         * @param mixed $value Parameter value
         */
        public function __set( $key, $value )
        {
            $this->params[ $key ] = $value;
        }
        
        /**
         * Prepare field source data
         */
        abstract protected function prepareSource();

        /**
         * Constructor for field source
         * 
         * @param Array $field_row Array with view field properties
         * @param string $list_obj_db_name Main list (on which register is based) table name 
         */
        public function __construct($field_row, $list_obj_db_name)
        {            
            $this->field_row = $field_row;
            $this->list_obj_db_name = $list_obj_db_name;
        }
    }

}
