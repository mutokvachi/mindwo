<?php

namespace App\Libraries\View\Operations
{
    /**
     * View field operation factory class
     */
    class OperationFactory
    {
        
        /**
         * Factory for field operation (WHERE criteria builder)
         * 
         * @param string $field_where_name Field name used in WHERE criteria
         * @param Array $field_row Array with view field properties
         */
        public static function build_operation($field_where_name, $field_row)
        {            
            $operation = str_replace(" ", "_", trim($field_row->operation));
            $class = "App\\Libraries\\View\\Operations\\Operation_" . $operation;
            
            if (!class_exists($class)) {
                $class = "App\\Libraries\\View\\Operations\\Operation_default";                
            }
            
            return new $class($field_where_name, $field_row);
        }
        
    }

}