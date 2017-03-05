<?php
namespace App\Libraries\DataView\Formatters 
{   
    /**
    *
    * Lauku formātu klase
    *
    *
    * Objekts definē lauku formāta opcijas
    *
    */
    
    abstract class FormatAbstract
    {
        public $align = "left";
        public $value = "";
        
        protected $values = array();

        public function __get( $key )
        {
            if (!isset($this->values[ $key ])) {
                return false;
            }
            
            return $this->values[ $key ];
        }

        public function __set( $key, $value )
        {
            $this->values[ $key ] = $value;
        }
    }
}