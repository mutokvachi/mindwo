<?php

namespace App\Libraries\Structure\Types
{

    use App\Exceptions;
    use DB;
    
    class FieldTypeFactory
    {
        /**
          *
          * Sistēmas struktūras reģistra lauka tipa izveidošanas klase
          *
          *
          * Objekts izveido sistēmas struktūras reģistra lauka tipa klasi
          *
         */

        /**
         * Izveido sistēmas struktūras reģistra lauka tipa objektu
         * 
         * @param   integer $list_id     Reģistra ID
         * @param   string  $field_name  Tabulas lauka nosaukums
         * @return Object               Lauka tipa objekts
         */

        public static function build_type($table_name, $list_id, $field_name)
        {
            $field_obj = null;
            if ($field_name == "id")
            {
                $type = "id";
            }
            else
            {
                $field_obj = DB::connection()->getDoctrineColumn($table_name, $field_name);
                $type = $field_obj->getType()->getName();
            }
            
            $class = "App\\Libraries\\Structure\\Types\FieldType_" . $type;

            if (class_exists($class))
            {
                return new $class($table_name, $list_id, $field_name, $field_obj);
            }
            else
            {
                throw new Exceptions\DXCustomException("Neatbalstīts reģistra lauka tips '" . $type . "'!");
            }
        }
        
        public static function startsWith($haystack, $needle) {
            // search backwards starting from haystack length characters from the end
            return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
        }
        
        public static function endsWith($haystack, $needle) {
            // search forward starting from end minus needle length characters
            return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
        }

    }

}