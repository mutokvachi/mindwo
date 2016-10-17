<?php

namespace App\Libraries\FieldsImport
{

    /**
     * Factory for field importing from Excel
     */
    class FieldImportFactory
    {

        public static function build_field($excel_value, $fld, $tmp_dir)
        {
            $class = "App\\Libraries\\FieldsImport\\FieldImport_" . $fld->type_sys_name;

            if (!class_exists($class)) {
                $class = "App\\Libraries\\FieldsImport\\FieldImport_default";
            }

            return new $class($excel_value, $fld, $tmp_dir);
        }

    }

}