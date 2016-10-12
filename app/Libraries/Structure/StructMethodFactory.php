<?php

namespace App\Libraries\Structure
{

    use App\Exceptions;
    use DB;
    
    class StructMethodFactory
    {
        /**
          *
          * Sistēmas struktūras veidošanas metodes izveidošanas klase
          *
          *
          * Objekts izveido sistēmas struktūras veidošanas klasi
          *
         */

        /**
         * Izveido sistēmas struktūras veidošanas objektu
         * 
         * @param  string $method_name   Struktūras veidošanas metodes nosaukums
         * @return Object                Struktūras veidošanas metodes objekts
         */

        public static function build_method($method_name)
        {
            $class = "App\\Libraries\\Structure\\StructMethod_" . $method_name;

            if (class_exists($class))
            {
                return new $class();
            }
            else
            {
                throw new Exceptions\DXCustomException("Neatbalstīta struktūras veidošanas metode '" . $method_name . "'!");
            }
        }

    }

}