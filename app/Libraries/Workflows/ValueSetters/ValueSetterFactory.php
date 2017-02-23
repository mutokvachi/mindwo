<?php

namespace App\Libraries\Workflows\ValueSetters
{    
    /**
     * Custom workflow activity factory class
     */
    class ValueSetterFactory
    {
        /**
        * Constructor for field value setter
        * 
        * @param integer $item_id Item ID
        * @param string $table_name Name of table which will be udpated
        * @param object $fld Field object
        * @param mixed $val Field value (before formating)
        */
        public static function build_setter($item_id, $table_name, $fld, $val)
        {
            $class = "App\\Libraries\\Workflows\\ValueSetters\\ValueSetter_" . $fld->fld_type;
            
            if (!class_exists($class)) {
                $class = "App\\Libraries\\Workflows\\ValueSetters\\ValueSetter_default";
            }
            
            return new $class($item_id, $table_name, $fld, $val);
        }
        
    }

}