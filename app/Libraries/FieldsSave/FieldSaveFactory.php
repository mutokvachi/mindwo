<?php

namespace App\Libraries\FieldsSave
{

    class FieldSaveFactory
    {
        /**
         *
         * Formas saglabāšanas lauka izveidošanas klase
         *
         */

        /**
         * Izveido formas lauka saglabāšanas klasi
         * 
         * @param Request $request  POST/GET pieprasījuma objekts
         * @param Object $fld       Lauka objekts (no tabulas dx_lists_fields)
         * @param integer $item_id  Ieraksta ID
         * @return \App\Libraries\FieldsSave\class
         */
        public static function build_field($request, $fld, $item_id)
        {
            $class = "App\\Libraries\\FieldsSave\\FieldSave_" . $fld->type_sys_name;

            if (!class_exists($class))
            {
                $class = "App\\Libraries\\FieldsSave\\FieldSave_default";
            }

            return new $class($request, $fld, $item_id);
        }

    }

}