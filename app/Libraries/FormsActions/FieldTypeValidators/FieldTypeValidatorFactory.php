<?php

namespace App\Libraries\FormsActions\FieldTypeValidators
{

    /**
     * field type validator factory class
     */
    class FieldTypeValidatorFactory
    {

        /**
         * Field type validator builder
         *
         * @param  Request $request POST/GET request object
         * @param Object $fld Field type db row
         * @return \App\Libraries\FormsActions\FieldTypeValidators\class
         */
        public static function build_validator($request, $fld)
        {
            $class = "App\\Libraries\\FormsActions\\FieldTypeValidators\\FieldTypeValidator_" . $fld->sys_name;
            
            if (!class_exists($class)) {
                $class = "App\\Libraries\\FormsActions\\FieldTypeValidators\\FieldTypeValidator_default";
            }

            return new $class($request, $fld);
        }

    }

}